<?php

namespace RedJasmine\Order\Services\Orders;


use Exception;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderInfo;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderProductInfo;
use RedJasmine\Order\OrderService;
use RedJasmine\Order\ValueObjects\OrderProductObject;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Exceptions\AbstractException;
use RedJasmine\Support\Helpers\ID\Snowflake;
use RedJasmine\Support\Traits\Services\ServiceExtends;
use Throwable;


/**
 * @mixin  OrderService
 */
class OrderCreatorService
{
    use ServiceExtends;

    protected UserInterface $buyer;

    protected UserInterface $seller;

    protected array $validators = [];

    protected array $initPipelines = [];

    protected ?Order $order = null;


    public function __construct(protected OrderService $service)
    {
        $this->order = new Order();
        $this->order->setRelation('info', new OrderInfo());
        $this->order->setRelation('products', collect());
    }

    public function setOrderParameters($parameters) : static
    {
        $this->order->setParameters($parameters);
        return $this;
    }

    public function addInitPipelines($pipeline) : static
    {
        $this->initPipelines[] = $pipeline;
        return $this;
    }


    /**
     * 创建订单
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function create() : Order
    {
        // 计算金额
        $this->calculate();
        // 订单验证 TODO
        $this->validate();
        try {
            DB::beginTransaction();
            app(Pipeline::class)
                ->send($this->order)
                ->through(Config::get('red-jasmine.order.pipelines.create', []))
                ->then(function ($order) {
                    return $order;
                });
            $this->order->id = $this->buildID();
            $this->order->products->each(function ($product) {
                $product->id           = $product->id ?? $this->buildID();
                $product->order_status = $this->order->order_status;
            });

            $this->order->save();
            $this->order->info()->save($this->order->info);
            $this->order->products()->saveMany($this->order->products);
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        return $this->order;

    }

    public function calculate() : static
    {
        $this->init();
        // 计算商品金额
        $this->calculateProducts();
        // 计算订单金额
        $this->calculateOrder();

        return $this;
    }

    protected function init() : static
    {
        $this->order->buyer_type      = $this->buyer->getType();
        $this->order->buyer_id        = $this->buyer->getID();
        $this->order->buyer_nickname  = $this->buyer->getNickname();
        $this->order->seller_type     = $this->seller->getType();
        $this->order->seller_id       = $this->seller->getID();
        $this->order->seller_nickname = $this->seller->getNickname();
        $this->order->creator_type    = $this->getOperator()->getType();
        $this->order->creator_id      = $this->getOperator()->getID();

        // 初始化管道
        app(Pipeline::class)
            ->send($this->order)
            ->through($this->getInitPipelines())
            ->then(function ($order) {
                return $order;
            });

        return $this;
    }

    public function getInitPipelines() : array
    {
        $pipelines = Config::get('red-jasmine.order.pipelines.init', []);
        return array_merge($pipelines, $this->initPipelines);
    }

    protected function calculateProducts() : static
    {
        foreach ($this->order->products as $product) {
            // 商品金额
            $product->amount = bcmul($product->num, $product->price, 2);
            // 成本金额
            $product->cost_amount = bcmul($product->num, $product->price, 2);
            // 计算税费
            $product->tax_amount = bcadd($product->tax_amount, 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->discount_amount, 0, 2);
            // 付款金额
            $product->payment_amount = bcsub(bcadd($product->amount, $product->tax_amount, 2), $product->discount_amount, 2);
            // 分摊优惠
            $product->divide_discount_amount = bcadd(0, 0, 2);
            // 分摊后付款金额
            $product->divided_payment_amount = bcsub($product->payment_amount, $product->divide_discount_amount, 2);
        }

        return $this;

    }

    protected function calculateOrder() : static
    {
        // 计算商品金额
        $this->order->total_amount = $this->order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payment_amount, 2);
        }, 0);
        // 统计成本
        $this->order->cost_amount = $this->order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 邮费
        $this->order->freight_amount = bcadd(0, 0, 2);
        // 订单优惠
        $this->order->discount_amount = bcadd(0, 0, 2);
        // 计算付款 金额 = 商品总金额 + 邮费 - 优惠
        $this->order->payment_amount = bcsub(bcadd($this->order->total_amount, $this->order->freight_amount, 2), $this->order->discount_amount, 2);
        // TODO 计算分摊
        return $this;
    }

    public function validate()
    {
        $this->init();
        foreach ($this->validators as $validator) {
            // TODO 验证
        }
    }

    /**
     * 生成订单ID
     * @return int
     * @throws Exception
     */
    public function buildID() : int
    {
        return Snowflake::getInstance()->nextId();
    }

    public function getOrder() : Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order) : OrderCreatorService
    {
        $this->order = $order;
        return $this;
    }

    /**
     * 计算分摊优惠
     * @return void
     */
    public function calculateDivideDiscount()
    {
        $this->order->discount_amount;

        $totalAmount = $this->order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->amount, 2);
        }, 0);
        // 对商品进行排序 从小到大
        $products = $this->order->products->sortBy('product_amount')->values();
    }

    /**
     * 添加商品
     *
     * @param OrderProduct $product
     *
     * @return $this
     * @throws Exception
     */
    public function addProduct(OrderProduct $product) : static
    {
        if ($product->relationLoaded('info') === false) {
            $product->setRelation('info', new OrderProductInfo());
        }
        $this->order->products->add($product);
        return $this;
    }

    public function getSeller() : UserInterface
    {
        return $this->seller;
    }

    public function setSeller(UserInterface $seller) : OrderCreatorService
    {
        $this->seller = $seller;
        return $this;
    }

    public function getBuyer() : UserInterface
    {
        return $this->buyer;
    }

    public function setBuyer(UserInterface $buyer) : OrderCreatorService
    {
        $this->buyer                 = $buyer;
        $this->order->buyer_type     = $buyer->getType();
        $this->order->buyer_id       = $buyer->getID();
        $this->order->buyer_nickname = $buyer->getNickname();
        return $this;
    }

}
