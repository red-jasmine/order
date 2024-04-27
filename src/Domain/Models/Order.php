<?php


namespace RedJasmine\Order\Domain\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use RedJasmine\Order\Domain\Enums\OrderRefundStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Enums\TradePartyEnums;
use RedJasmine\Order\Domain\Events\OrderCanceledEvent;
use RedJasmine\Order\Domain\Events\OrderConfirmedEvent;
use RedJasmine\Order\Domain\Events\OrderCreatedEvent;
use RedJasmine\Order\Domain\Events\OrderFinishedEvent;
use RedJasmine\Order\Domain\Events\OrderPaidEvent;
use RedJasmine\Order\Domain\Events\OrderPayingEvent;
use RedJasmine\Order\Domain\Events\OrderProgressEvent;
use RedJasmine\Order\Domain\Events\OrderShippedEvent;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Order\Domain\Exceptions\OrderExceptionCodeEnum;
use RedJasmine\Order\Domain\Models\ValueObjects\Progress;
use RedJasmine\Order\Domain\Services\OrderRefundService;
use RedJasmine\Order\Services\OrderService;
use RedJasmine\Support\Casts\AesEncrypted;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Data\UserData;
use RedJasmine\Support\Foundation\HasServiceContext;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use Spatie\LaravelData\WithData;
use Throwable;


class Order extends Model
{
    use HasServiceContext;

    use WithData;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;

    public bool $withTradePartiesNickname = true;

    public $incrementing = false;


    protected $dispatchesEvents = [
        'created'   => OrderCreatedEvent::class,
        'canceled'  => OrderCanceledEvent::class,
        'paying'    => OrderPayingEvent::class,
        'paid'      => OrderPaidEvent::class,
        'shipped'   => OrderShippedEvent::class,
        'progress'  => OrderProgressEvent::class,
        'finished'  => OrderFinishedEvent::class,
        'confirmed' => OrderConfirmedEvent::class,
    ];

    protected $observables = [
        'paying',
        'paid',
        'shipped',
        'progress',
        'canceled',
        'confirmed',
    ];


    protected $casts = [
        'order_type'       => OrderTypeEnum::class,
        'shipping_type'    => ShippingTypeEnum::class,
        'order_status'     => OrderStatusEnum::class,
        'payment_status'   => PaymentStatusEnum::class,
        'shipping_status'  => ShippingStatusEnum::class,
        'refund_status'    => OrderRefundStatusEnum::class,
        'created_time'     => 'datetime',
        'payment_time'     => 'datetime',
        'close_time'       => 'datetime',
        'shipping_time'    => 'datetime',
        'collect_time'     => 'datetime',
        'dispatch_time'    => 'datetime',
        'signed_time'      => 'datetime',
        'confirm_time'     => 'datetime',
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
        $orderProduct->creator = $this->getOperator();
        $this->products->add($orderProduct);
        return $this;
    }


    public function setAddress(OrderAddress $orderAddress) : static
    {
        $orderAddress->creator = $this->getOperator();
        $this->setRelation('address', $orderAddress);
        return $this;
    }

    /**
     * 计算金额
     * @return $this
     */
    public function calculateAmount() : static
    {
        // 统计商品金额
        $this->calculateProductsAmount();
        // 汇总订单金额
        $this->calculateOrderAmount();
        // 分摊订单数据
        $this->calculateDivideDiscountAmount();
        return $this;
    }

    /**
     * 计算商品金额
     * @return void
     */
    protected function calculateProductsAmount() : void
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
            // 应付金额  = 商品金额 - 单品优惠 + 税费
            $product->payable_amount = bcsub(bcadd($product->product_amount, $product->tax_amount, 2), $product->discount_amount, 2);
            // 实付金额 完成支付时
            $product->payment_amount = $product->payment_amount ?? 0;

            // 佣金
            $product->commission_amount = $product->commission_amount ?? 0;

        }
    }


    protected function calculateOrderAmount() : void
    {
        $order = $this;
        // 商品金额
        $order->product_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->product_amount, 2);
        }, 0);
        // 商品成本
        $order->cost_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 总费用
        $order->tax_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->tax_amount, 2);
        }, 0);
        // 总佣金
        $order->commission_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->commission_amount, 2);
        }, 0);

        // 邮费
        $order->freight_amount = bcadd($order->freight_amount, 0, 2);
        // 订单优惠
        $order->discount_amount = bcadd($order->discount_amount, 0, 2);

        // 商品应付汇总
        $order->product_payable_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payable_amount, 2);
        }, 0);

        // 订单应付金额 = 商品总应付金额  - 优惠 + 邮费
        $order->payable_amount = bcsub(bcadd($order->product_payable_amount, $order->freight_amount, 2), $order->discount_amount, 2);

    }

    /**
     * 计算分摊优惠
     * @return void
     */
    protected function calculateDivideDiscountAmount() : void
    {
        $order = $this;
        $order->discount_amount;

        // 对商品进行排序 从小到大
        $products     = $order->products->sortBy('product_amount')->values();
        $productCount = count($products);
        // 剩余优惠金额
        $discountAmountSurplus = $this->discount_amount;
        /**
         * @var $product OrderProduct
         */
        foreach ($products as $key => $product) {
            // 最后一个
            $product->divided_discount_amount = 0;
            if ($key === $productCount - 1) {
                $product->divided_discount_amount = $discountAmountSurplus;
            } else {
                if (bccomp($product->payable_amount, 0, 2) !== 0) {
                    $product->divided_discount_amount = bcmul($this->discount_amount, bcdiv($product->payable_amount, $this->product_payable_amount, 4), 2);
                } else {
                    $product->divided_discount_amount = 0;
                }
            }
            $discountAmountSurplus           = bcsub($discountAmountSurplus, $product->divided_discount_amount, 2);
            $product->divided_payment_amount = bcsub($product->payable_amount, $product->divided_discount_amount, 2);

        }


    }


    public function create() : static
    {

        $this->created_time = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->order_id     = $this->id;
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

        $this->creator = $this->getOperator();

        $this->fireModelEvent('created');

        return $this;
    }


    public function addPayment(OrderPayment $orderPayment) : void
    {
        $orderPayment->creator = $this->getOperator();
        $this->payments->add($orderPayment);
    }


    public function addLogistics(OrderLogistics $logistics) : void
    {
        $this->logistics->add($logistics);
    }


    /**
     * 有效 子单数量
     * @return int
     */
    public function productEffectiveCount() : int
    {
        $count = 0;

        $this->products->each(function (OrderProduct $orderProduct) use (&$count) {
            if ($orderProduct->isEffective()) {
                $count++;
            }
        });

        return $count;
    }

    public function shipping() : void
    {
        $effectiveAndNotShippingCount = 0;
        $this->products->each(function (OrderProduct $orderProduct) use (&$effectiveAndNotShippingCount) {
            if ($orderProduct->isEffective() && in_array($orderProduct->shipping_status, [ ShippingStatusEnum::NIL, ShippingStatusEnum::WAIT_SEND ], true)) {
                $effectiveAndNotShippingCount++;
            }
        });
        // 如果还有未发货的订单商品 那么订单只能是部分发货
        $this->shipping_status = $effectiveAndNotShippingCount > 0 ? ShippingStatusEnum::PART_SHIPPED : ShippingStatusEnum::SHIPPED;
        $this->shipping_time   = $order->shipping_time ?? now();
        $this->updater         = $this->getOperator();
        $this->fireModelEvent('shipped');
    }


    /**
     * @param string|null $reason
     *
     * @return void
     * @throws OrderException
     */
    public function cancel(?string $reason = null) : void
    {
        // 什么情况下可以取消
        if ($this->order_status === OrderStatusEnum::CANCEL) {
            throw OrderException::newFromCodes(OrderException::ORDER_STATUS_NOT_ALLOW);
        }
        // 未发货、未支付、情况下可以取消
        if (in_array($this->payment_status, [ PaymentStatusEnum::PAID, PaymentStatusEnum::PART_PAY, PaymentStatusEnum::NO_PAYMENT, ], true)) {
            throw OrderException::newFromCodes(OrderException::PAYMENT_STATUS_NOT_ALLOW);
        }
        $this->order_status  = OrderStatusEnum::CANCEL;
        $this->cancel_reason = $reason;
        $this->close_time    = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->order_status = OrderStatusEnum::CANCEL;
            $orderProduct->close_time   = now();
        });
        $this->updater = $this->getOperator();
        $this->fireModelEvent('canceled');
    }

    /**
     * 发起支付
     *
     * @param OrderPayment $orderPayment
     *
     * @return void
     * @throws OrderException
     */
    public function paying(OrderPayment $orderPayment) : void
    {
        if (!in_array($this->payment_status, [ PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::NIL ], true)) {
            throw  OrderException::newFromCodes(OrderException::PAYMENT_STATUS_NOT_ALLOW);
        }
        // 添加支付单
        $orderPayment->order_id = $this->id;
        $orderPayment->seller   = $this->seller;
        $orderPayment->buyer    = $this->buyer;
        $orderPayment->status   = PaymentStatusEnum::PAYING;
        $orderPayment->creator  = $this->getOperator();
        $this->addPayment($orderPayment);
        // 设置为支付中
        if (in_array($this->payment_status, [ PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::NIL ], true)) {
            $this->payment_status = PaymentStatusEnum::PAYING;
        }
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->payment_status = PaymentStatusEnum::PAYING;
        });
        $this->updater = $this->getOperator();
        $this->fireModelEvent('paying');
    }


    /**
     * @param OrderPayment $orderPayment
     *
     * @return void
     * @throws OrderException
     */
    public function paid(OrderPayment $orderPayment) : void
    {
        if (!in_array($this->payment_status, [ PaymentStatusEnum::NIL, PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::PAYING, PaymentStatusEnum::PART_PAY ], true)) {
            throw  OrderException::newFromCodes(OrderException::PAYMENT_STATUS_NOT_ALLOW);
        }

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
     * 订单确认
     *
     * @param int|null $orderProductId
     *
     * @return void
     * @throws OrderException
     */
    public function confirm(?int $orderProductId = null) : void
    {

        if (in_array($this->order_status, [ OrderStatusEnum::CANCEL, OrderStatusEnum::FINISHED, OrderStatusEnum::CLOSED ], true)) {
            throw new OrderException('订单完成');
        }
        // 只有在 部分发货情况下  才允许 传入子单号 单独确认搜获
        if (filled($orderProductId)) {
            // 子单 分开确认
            // 如果是部分发货  子单号必须填写
            if ($this->shipping_status === ShippingStatusEnum::PART_SHIPPED) {
                throw new OrderException('发货状态不一致');
            }

            $orderProduct = $this->products->where('id', $orderProductId)->firstOrFail();

            if ($orderProduct->shipping_status !== ShippingStatusEnum::SHIPPED) {
                throw new OrderException('子单未发货完成');
            }
            $orderProduct->signed_time  = now();
            $orderProduct->confirm_time = now();

        } else {
            if ($this->shipping_status !== ShippingStatusEnum::SHIPPED) {
                throw new OrderException('发货状态不一致');
            }

            $this->products->each(function (OrderProduct $orderProduct) {
                if ($orderProduct->isEffective()) {
                    // 已经确认了的 无需再次确认
                    $orderProduct->confirm_time = $orderProduct->confirm_time ?? now();
                    $orderProduct->signed_time  = $orderProduct->confirm_time ?? now();
                }
            });

            $this->confirm_time = $this->confirm_time ?? now();
            $this->signed_time  = $this->signed_time ?? now();
        }


        $this->fireModelEvent('confirmed');
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

        $this->fireModelEvent('progress');
    }


    public function refunds() : HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_id', 'id');
    }

    public function createRefund(OrderRefund $orderRefund) : void
    {
        app(OrderRefundService::class)->create($this, $orderRefund);

    }


    public function remarks(TradePartyEnums $tradeParty, string $remarks = null, ?int $orderProductId = null) : void
    {
        $field = $tradeParty->value . '_remarks';
        if ($orderProductId) {
            $this->products->where('id', $orderProductId)->firstOrFail()->info->{$field} = $remarks;
        } else {
            $this->info->{$field} = $remarks;
        }
    }

    public function setSellerCustomStatus(string $sellerCustomStatus, ?int $orderProductId = null) : void
    {
        if ($orderProductId) {
            $this->products->where('id', $orderProductId)->firstOrFail()->seller_custom_status = $sellerCustomStatus;
        } else {
            $this->seller_custom_status = $sellerCustomStatus;
        }
    }


    /**
     * @param TradePartyEnums $tradeParty
     *
     * @return void
     * @throws OrderException
     */
    public function hiddenOrder(TradePartyEnums $tradeParty) : void
    {
        switch ($tradeParty) {
            case TradePartyEnums::SELLER:
                $this->is_seller_delete = true;
                break;
            case TradePartyEnums::BUYER:
                $this->is_buyer_delete = true;
                break;
            default:
                throw new OrderException('交易方不支持');
                break;
        }

    }

}
