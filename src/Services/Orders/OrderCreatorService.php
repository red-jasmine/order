<?php

namespace RedJasmine\Order\Services\Orders;


use Exception;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderAddress;
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

    protected array $pipelines = [];

    protected ?Order $order = null;


    public function __construct(protected OrderService $service)
    {
        $this->order = new Order();
        $this->order->setRelation('info', new OrderInfo());
        $this->order->setRelation('products', collect());

        if ($this->getOperator()) {
            $this->order->creator_type = $this->getOperator()->getType();
            $this->order->creator_id   = $this->getOperator()->getID();
        }
    }

    public function setOrderParameters($parameters) : static
    {
        $this->order->setParameters($parameters);
        return $this;
    }

    public function addPipelines($pipeline) : static
    {
        $this->pipelines[] = $pipeline;
        return $this;
    }

    public function getPipelines() : array
    {
        $pipelines = Config::get('red-jasmine.order.pipelines.creation', []);
        return array_merge($this->pipelines, $pipelines);
    }


    /**
     * 创建订单
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function create() : Order
    {
        try {
            DB::beginTransaction();
            // 数据验证
            $order = app(Pipeline::class)
                ->send($this->order)
                ->through($this->getPipelines())
                ->then(function (Order $order) {
                    $order->id = $this->buildID();
                    $order->products->each(function ($product) use ($order) {
                        $product->id           = $product->id ?? $this->buildID();
                        $product->order_status = $order->order_status;
                    });
                    $order->save();
                    $order->info()->save($order->info);
                    $order->products()->saveMany($order->products);
                    return $order;
                });
            DB::commit();
            return $order;
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
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
     * @param array|Collection|OrderProduct[] $products
     *
     * @return static
     * @throws Exception
     */
    public function setProducts(array|Collection $products) : static
    {
        $this->order->setRelation('products', []);
        foreach ($products as $product) {
            $this->addProduct($product);
        }
        return $this;
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
        $this->seller                 = $seller;
        $this->order->seller_type     = $this->seller->getType();
        $this->order->seller_id       = $this->seller->getID();
        $this->order->seller_nickname = $this->seller->getNickname();
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
