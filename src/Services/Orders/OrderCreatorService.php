<?php

namespace RedJasmine\Order\Services\Orders;


use Exception;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\OrderStatusEnums;
use RedJasmine\Order\Enums\Orders\OrderTypeEnums;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnums;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnums;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderInfo;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\OrderService;
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

    protected UserInterface  $owner;
    protected UserInterface  $buyer;
    protected UserInterface  $seller;
    protected ?UserInterface $guide      = null;
    protected ?UserInterface $channel    = null;
    protected ?UserInterface $store      = null;
    protected array          $validators = [];

    /**
     * 订单类型
     * @var OrderTypeEnums
     */
    protected OrderTypeEnums $orderType = OrderTypeEnums::MALL;
    /**
     * 发货类型
     * @var ShippingTypeEnums
     */
    protected ShippingTypeEnums $shippingType;

    /**
     * 订单状态
     * @var OrderStatusEnums
     */
    protected OrderStatusEnums $orderStatus = OrderStatusEnums::WAIT_BUYER_PAY;

    /**
     * 发货状态
     * @var ShippingStatusEnums
     */
    protected ShippingStatusEnums $shippingStatus = ShippingStatusEnums::WAIT_SEND;

    /**
     * 发货状态
     * @var PaymentStatusEnums
     */
    protected PaymentStatusEnums $paymentStatus = PaymentStatusEnums::WAIT_PAY;


    protected ?Order     $order     = null;
    protected ?OrderInfo $orderInfo = null;

    public function __construct(protected OrderService $service)
    {
        $this->initOrder();
    }


    // 验证


    /**
     * 生成订单ID
     * @return int
     * @throws Exception
     */
    public function buildID() : int
    {
        return Snowflake::getInstance()->nextId();
    }


    protected function fillOrder() : static
    {
        // 卖家
        $this->order->seller_type     = $this->seller->getType();
        $this->order->seller_id       = $this->seller->getID();
        $this->order->seller_nickname = $this->seller->getNickname();
        // 买家
        $this->order->buyer_type     = $this->buyer->getType();
        $this->order->buyer_id       = $this->buyer->getID();
        $this->order->buyer_nickname = $this->buyer->getNickname();
        // 订单类型
        $this->order->order_type    = $this->orderType;
        $this->order->shipping_type = $this->shippingType;
        // 订单信息
        $this->order->title = '';
        // 订单状态
        $this->order->order_status = $this->orderStatus;
        // 发货状态
        $this->order->shipping_status = $this->shippingStatus;
        // 支付状态
        $this->order->payment_status = $this->paymentStatus;
        // 时间
        $this->order->created_time = now();
        // 渠道
        if ($this->channel) {
            $this->order->channel_type = $this->channel->getType();
            $this->order->channel_id   = $this->channel->getID();
        }
        // 门店
        if ($this->store) {
            $this->order->store_type = $this->store->getType();
            $this->order->store_id   = $this->store->getID();
        }
        // 导购、推荐
        if ($this->guide) {
            $this->order->guide_type = $this->guide->getType();
            $this->order->guide_id   = $this->guide->getID();
        }
        if ($this->getOperator()) {
            $this->order->creator_type = $this->getOperator()->getType();
            $this->order->creator_id   = $this->getOperator()->getID();
        }

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
                $product->id = $product->id ?? $this->buildID();
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


    /**
     * @param ProductInterface $product
     *
     * @return OrderProduct
     * @throws Exception
     */
    protected function initProduct(ProductInterface $product) : OrderProduct
    {
        if (($product instanceof OrderProduct) === false) {
            $product = OrderProduct::make($product);
        }
        $product->order_status    = $this->orderStatus;
        $product->shipping_status = $this->shippingStatus;
        return $product;
    }

    public function calculate() : static
    {
        // 计算商品金额
        $this->calculateProducts();
        // 计算订单金额
        $this->calculateOrder();
        $this->fillOrder();
        return $this;
    }

    public function initOrder() : static
    {
        $this->order = new Order();
        $this->order->setRelation('info', new OrderInfo());
        $this->order->setRelation('products', collect());
        return $this;
    }

    public function getOrder() : Order
    {
        return $this->order;
    }

    protected function calculateProducts() : static
    {
        foreach ($this->order->products as $product) {
            // 商品金额
            $product->amount = bcmul($product->getNum(), $product->getPrice(), 2);
            // 成本金额
            $product->cost_amount = bcmul($product->getNum(), $product->getPrice(), 2);
            // 计算税费
            $product->tax_amount = bcadd($product->getTaxAmount(), 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->getDiscountAmount(), 0, 2);
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

        // TODO 计算分摊  是在营销中心进行计算的


    }


    public function validate()
    {


        foreach ($this->validators as $validator) {
            // TODO 验证
        }


    }

    /**
     * 添加商品
     *
     * @param ProductInterface $product
     *
     * @return $this
     * @throws Exception
     */
    public function addProduct(ProductInterface $product) : static
    {
        $product = $this->initProduct($product);
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
        $this->buyer = $buyer;
        return $this;
    }

    public function setGuide(?UserInterface $guide) : OrderCreatorService
    {
        $this->guide = $guide;
        return $this;
    }

    public function setChannel(?UserInterface $channel) : OrderCreatorService
    {
        $this->channel = $channel;
        return $this;
    }

    public function setStore(?UserInterface $store) : OrderCreatorService
    {
        $this->store = $store;
        return $this;
    }

    public function setValidators(array $validators) : OrderCreatorService
    {
        $this->validators = $validators;
        return $this;
    }

    public function setOrderType(OrderTypeEnums $orderType) : OrderCreatorService
    {
        $this->orderType = $orderType;
        return $this;
    }

    public function setShippingType(ShippingTypeEnums $shippingType) : OrderCreatorService
    {
        $this->shippingType = $shippingType;
        return $this;
    }

    public function setOrderStatus(OrderStatusEnums $orderStatus) : OrderCreatorService
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    public function setShippingStatus(ShippingStatusEnums $shippingStatus) : OrderCreatorService
    {
        $this->shippingStatus = $shippingStatus;
        return $this;
    }

    public function setPaymentStatus(PaymentStatusEnums $paymentStatus) : OrderCreatorService
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function setOrder(?Order $order) : OrderCreatorService
    {
        $this->order = $order;
        return $this;
    }


}
