<?php


namespace RedJasmine\Order\Domain\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Events\OrderCanceledEvent;
use RedJasmine\Order\Domain\Events\OrderCreatedEvent;
use RedJasmine\Order\Domain\Events\OrderFinishedEvent;
use RedJasmine\Order\Domain\Events\OrderPaidEvent;
use RedJasmine\Order\Domain\Events\OrderPayingEvent;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Order\Domain\Models\ValueObjects\Progress;
use RedJasmine\Support\Casts\AesEncrypted;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Data\UserData;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use Spatie\LaravelData\WithData;


class Order extends Model
{

    use WithData;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;

    public bool $withTradePartiesNickname = true;

    public $incrementing = false;


    protected $dispatchesEvents = [
        'created'  => OrderCreatedEvent::class,
        'canceled' => OrderCanceledEvent::class,
        'paying'   => OrderPayingEvent::class,
        'paid'     => OrderPaidEvent::class,
        'finished' => OrderFinishedEvent::class
    ];


    protected $casts = [
        'order_type'       => OrderTypeEnum::class,
        'shipping_type'    => ShippingTypeEnum::class,
        'order_status'     => OrderStatusEnum::class,
        'payment_status'   => PaymentStatusEnum::class,
        'shipping_status'  => ShippingStatusEnum::class,
        'refund_status'    => RefundStatusEnum::class,
        'created_time'     => 'datetime',
        'payment_time'     => 'datetime',
        'close_time'       => 'datetime',
        'shipping_time'    => 'datetime',
        'collect_time'     => 'datetime',
        'dispatch_time'    => 'datetime',
        'signed_time'      => 'datetime',
        'end_time'         => 'datetime',
        'refund_time'      => 'datetime',
        'rate_time'        => 'datetime',
        'contact'          => AesEncrypted::class,
        'is_seller_delete' => 'boolean',
        'is_buyer_delete'  => 'boolean',
    ];


    public function info() : HasOne
    {
        return $this->hasOne(OrderInfo::class, 'id', 'id');
    }

    public function products() : HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function address() : HasOne
    {
        return $this->hasOne(OrderAddress::class, 'id', 'id');
    }

    public function logistics() : MorphMany
    {
        return $this->morphMany(OrderLogistics::class, 'shippable');
    }

    public function payments() : HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

    public function guide() : Attribute
    {
        return Attribute::make(
            get: static fn(mixed $value, array $attributes) => UserData::from([ 'type' => $attributes['guide_type'], 'id' => $attributes['guide_id'], ]),
            set: static fn(?UserInterface $user) => [ 'guide_type' => $user?->getType(), 'guide_id' => $user?->getID(), ]
        );
    }

    public function store() : Attribute
    {
        return Attribute::make(
            get: static fn(mixed $value, array $attributes) => UserData::from([ 'type' => $attributes['store_type'], 'id' => $attributes['store_id'], ]),
            set: static fn(?UserInterface $user) => [ 'store_type' => $user?->getType(), 'store_id' => $user?->getID(), ]
        );
    }


    public function addProduct(OrderProduct $orderProduct) : static
    {
        $this->products->add($orderProduct);
        return $this;
    }


    public function setAddress(OrderAddress $orderAddress) : static
    {
        $this->setRelation('address', $orderAddress);
        return $this;
    }

    public function calculateAmount() : static
    {
        // 统计商品金额
        $this->calculateProducts();
        // 汇总订单金额
        $this->calculateOrder();
        // 分摊订单数据
        $this->calculateDivideDiscount();
        return $this;
    }


    protected function calculateProducts() : void
    {
        foreach ($this->products as $product) {
            // 商品总金额   < 0 TODO 验证金额

            $product->product_amount = bcmul($product->num, $product->price, 2);
            // 成本金额
            $product->cost_amount = bcmul($product->num, $product->cost_price, 2);
            // 计算税费
            $product->tax_amount = bcadd($product->tax_amount, 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->discount_amount, 0, 2);
            // 应付金额  = 商品金额 + 税费 - 单品优惠

            $product->payable_amount = bcsub(bcadd($product->product_amount, $product->tax_amount, 2), $product->discount_amount, 2);

            // 实付金额 完成支付时
            $product->payment_amount = 0;

        }
    }


    protected function calculateOrder() : void
    {
        $order = $this;
        // 商品金额
        $order->total_product_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->product_amount, 2);
        }, 0);
        // 商品成本
        $order->total_cost_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 商品应付
        $order->total_payable_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payable_amount, 2);
        }, 0);

        // 邮费
        $order->freight_amount = bcadd($order->freight_amount, 0, 2);
        // 订单优惠
        $order->discount_amount = bcadd($order->discount_amount, 0, 2);

        // 订单应付金额 = 商品总应付金额 + 邮费 - 优惠
        $order->payable_amount = bcsub(bcadd($order->total_payable_amount, $order->freight_amount, 2), $order->discount_amount, 2);

    }

    /**
     * 计算分摊优惠
     * @return void
     */
    protected function calculateDivideDiscount() : void
    {
        $order = $this;
        $order->discount_amount;
        // 对商品进行排序 从小到大
        $products = $order->products->sortBy('product_amount')->values();
        // TODO
    }

    /**
     * @return Order
     */
    public function create() : static
    {

        // 初始化订单状态
        // TODO 策略模式
        $this->order_status    = OrderStatusEnum::WAIT_BUYER_PAY;
        $this->payment_status  = null;
        $this->shipping_status = null;
        $this->refund_status   = null;
        $this->rate_status     = null;

        $this->created_time = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->order_id     = $this->id;
            $orderProduct->order_status = $this->order_status;
            $orderProduct->seller       = $this->seller;
            $orderProduct->buyer        = $this->buyer;
            $orderProduct->created_time = $this->created_time;
            $orderProduct->creator      = $this->creator;
        });
        if ($this->address) {
            $this->address->id = $this->id;
        }

        // 计算金额
        $this->calculateAmount();


        $this->fireModelEvent('created');

        return $this;
    }


    public function addPayment(OrderPayment $orderPayment) : void
    {
        $this->payments->add($orderPayment);
    }


    public function addLogistics(OrderLogistics $logistics) : void
    {
        $this->logistics->add($logistics);
    }


    public function shipping() : Order
    {
        $order = $this;
        // 查询未发货的订单商品
        // TODO  正常有效单订单商品 未退款
        $count = $order->products->whereIn('shipping_status', [ null, ShippingStatusEnum::WAIT_SEND ])->count();
        // 如果还有未发货的订单商品 那么订单只能是部分发货
        $order->shipping_status = $count > 0 ? ShippingStatusEnum::PART_SHIPPED : ShippingStatusEnum::SHIPPED;
        $order->shipping_time   = $order->shipping_time ?? now();
        // TODO 存在退款单 那么就直接关闭？

        return $order;
    }


    public function cancel(?string $reason = null) : void
    {
        $this->order_status  = OrderStatusEnum::CANCEL;
        $this->cancel_reason = $reason;
        $this->close_time    = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->order_status = OrderStatusEnum::CANCEL;
            $orderProduct->close_time   = now();
        });

        $this->fireModelEvent('canceled');
    }

    /**
     * 发起支付
     *
     * @param OrderPayment $orderPayment
     *
     * @return void
     */
    public function paying(OrderPayment $orderPayment) : void
    {
        // 添加支付单
        $orderPayment->order_id = $this->id;
        $orderPayment->seller   = $this->seller;
        $orderPayment->buyer    = $this->buyer;
        $orderPayment->status   = PaymentStatusEnum::PAYING;

        $this->addPayment($orderPayment);
        // 设置为支付中
        if (!$this->payment_status) {
            $this->payment_status = PaymentStatusEnum::PAYING;
        }
        $this->fireModelEvent('paying');
    }


    public function paid(OrderPayment $orderPayment) : void
    {
        $orderPayment->status = PaymentStatusEnum::PAID;

        $this->payment_amount = bcadd($this->payment_amount, $orderPayment->payment_amount, 2);
        $this->payment_status = PaymentStatusEnum::PART_PAY;
        $this->payment_time   = $this->payment_time ?? now();
        if (bccomp($this->payment_amount, $this->payable_amount, 2) >= 0) {
            $this->payment_status = PaymentStatusEnum::PAID;
        }
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->payment_status = $this->payment_status;
            $orderProduct->payment_time   = $this->payment_time;
            // 全部支付成功是 才能设置 订单商品的支付金额
            if ($orderProduct->payment_status = PaymentStatusEnum::PAID) {
                $orderProduct->payment_amount = $orderProduct->payable_amount;
            }
        });

        $this->fireModelEvent('paid');
    }


    /**
     * @return void
     * @throws \RedJasmine\Order\Domain\Exceptions\OrderException
     */
    public function confirm() : void
    {

        if (in_array($this->order_status, [ OrderStatusEnum::CANCEL, OrderStatusEnum::FINISHED, OrderStatusEnum::CLOSED ], true)) {
            throw new OrderException('订单完成');
        }
        $this->order_status = OrderStatusEnum::FINISHED;
        $this->end_time     = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            // TODO 过滤 已退款的订单
            $orderProduct->order_status = $this->order_status;
            $orderProduct->end_time     = $this->end_time;
        });


        $this->fireModelEvent('finished');
    }


    public function setProductProgress(int $orderProductId, Progress $progress) : void
    {
        // TODO 是否允许 小于之前的值
        $orderProduct = $this->products->where('id', $orderProductId)->firstOrFail();

        if ($progress->progress > ($orderProduct->progress ?? 0)) {
            $orderProduct->progress = $progress->progress ?? $orderProduct->progress;
        } elseif ($progress->isAllowLess) {
            $orderProduct->progress = $progress->progress ?? $orderProduct->progress;
        }
        $orderProduct->progress_total = $progress->progressTotal ?? $orderProduct->progress_total;
        // 触发事件
        $this->fireModelEvent('progress');
    }
}
