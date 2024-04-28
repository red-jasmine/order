<?php

namespace RedJasmine\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domain\Enums\OrderRefundStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use Spatie\LaravelData\WithData;

class OrderProduct extends Model
{
    use WithData;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;


    public $incrementing = false;


    protected $casts = [
        'shipping_type'   => ShippingTypeEnum::class,
        'order_status'    => OrderStatusEnum::class,
        'shipping_status' => ShippingStatusEnum::class,
        'payment_status'  => PaymentStatusEnum::class,
        'refund_status'   => OrderRefundStatusEnum::class,
        'created_time'    => 'datetime',
        'payment_time'    => 'datetime',
        'close_time'      => 'datetime',
        'shipping_time'   => 'datetime',
        'collect_time'    => 'datetime',
        'dispatch_time'   => 'datetime',
        'signed_time'     => 'datetime',
        'confirm_time'    => 'datetime',
        'refund_time'     => 'datetime',
        'rate_time'       => 'datetime',
    ];

    protected $fillable = [
        'shipping_type',
        'product_type',
        'product_id',
        'sku_id',
        'num',
        'price',
    ];


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }


    public function info() : HasOne
    {
        return $this->hasOne(OrderProductInfo::class, 'id', 'id');
    }


    public function refunds() : HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_product_id', 'id');
    }


    public function cardKeys() : HasMany
    {
        return $this->hasMany(OrderProductCardKey::class, 'order_product_id', 'id');
    }

    public function addCardKey(OrderProductCardKey $cardKey) : void
    {
        $this->cardKeys->add($cardKey);
    }

    /**
     * 是否为有效单
     * @return bool
     */
    public function isEffective() : bool
    {
        // 没有全款退
        if ($this->refund_status === OrderRefundStatusEnum::ALL_REFUND) {
            return false;
        }
        return true;
    }


    /**
     * 最大退款金额
     * @return string
     */
    public function maxRefundAmount() : string
    {
        return bcsub($this->divided_payment_amount, $this->refund_amount, 2);
    }

    /**
     * 允许的售后退款类型
     * 允许的售后类型 和 订单状态、发货状态、支付状态、支付方式 相关
     * @return array
     */
    public function allowRefundTypes() : array
    {
        // TODO 根据 子商品单  的 售后服务
        $allowApplyRefundTypes = [];

        // 如果支付那么久可以申请退款
        if (in_array($this->payment_status, [ PaymentStatusEnum::PART_PAY, PaymentStatusEnum::PAID ], true)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::REFUND_ONLY;
            $allowApplyRefundTypes[] = RefundTypeEnum::RETURN_GOODS_REFUND;
        }
        // 如果已发货
        if (in_array($this->shipping_status, [
            ShippingStatusEnum::PART_SHIPPED,
            ShippingStatusEnum::SHIPPED,
        ],           true)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::EXCHANGE;
        }

        return $allowApplyRefundTypes;
    }


}
