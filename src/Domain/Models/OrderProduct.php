<?php

namespace RedJasmine\Order\Domain\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domain\Enums\OrderProductTypeEnum;
use RedJasmine\Order\Domain\Enums\OrderRefundStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\PromiseServiceTypeEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\Casts\PromiseServicesCastTransformer;
use RedJasmine\Order\Domain\Models\ValueObjects\PromiseServiceValue;
use RedJasmine\Support\Domain\Models\Casts\AmountCastTransformer;
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
        'order_product_type'      => OrderProductTypeEnum::class,
        'shipping_type'           => ShippingTypeEnum::class,
        'order_status'            => OrderStatusEnum::class,
        'shipping_status'         => ShippingStatusEnum::class,
        'payment_status'          => PaymentStatusEnum::class,
        'refund_status'           => OrderRefundStatusEnum::class,
        'created_time'            => 'datetime',
        'payment_time'            => 'datetime',
        'close_time'              => 'datetime',
        'shipping_time'           => 'datetime',
        'collect_time'            => 'datetime',
        'dispatch_time'           => 'datetime',
        'signed_time'             => 'datetime',
        'confirm_time'            => 'datetime',
        'refund_time'             => 'datetime',
        'rate_time'               => 'datetime',
        'price'                   => AmountCastTransformer::class,
        'cost_price'              => AmountCastTransformer::class,
        'tax_amount'              => AmountCastTransformer::class,
        'product_amount'          => AmountCastTransformer::class,
        'payable_amount'          => AmountCastTransformer::class,
        'payment_amount'          => AmountCastTransformer::class,
        'refund_amount'           => AmountCastTransformer::class,
        'discount_amount'         => AmountCastTransformer::class,
        'commission_amount'       => AmountCastTransformer::class,
        'divided_discount_amount' => AmountCastTransformer::class,
        'divided_payment_amount'  => AmountCastTransformer::class,
        'promise_services'        => PromiseServicesCastTransformer::class,
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
        $cardKey->seller   = $this->seller;
        $cardKey->buyer    = $this->buyer;
        $cardKey->order_id = $this->order_id;
        $this->progress    += $cardKey->num;
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
     * @return array
     */
    public function allowRefundTypes() : array
    {

        $allowApplyRefundTypes = [];

        // 退款
        if ($this->isAllowPromiseService(PromiseServiceTypeEnum::REFUND)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::REFUND;
            if (in_array($this->shipping_status, [ ShippingStatusEnum::PART_SHIPPED, ShippingStatusEnum::SHIPPED ], true)) {
                $allowApplyRefundTypes[] = RefundTypeEnum::RETURN_GOODS_REFUND;
            }
        }
        // 换货 只有物流发货才支持换货 TODO
        if (in_array($this->shipping_status, [ ShippingStatusEnum::PART_SHIPPED, ShippingStatusEnum::SHIPPED ], true)
            && $this->isAllowPromiseService(PromiseServiceTypeEnum::EXCHANGE)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::EXCHANGE;
        }
        // 保修
        if (in_array($this->shipping_status, [ ShippingStatusEnum::PART_SHIPPED, ShippingStatusEnum::SHIPPED ], true)
            && $this->isAllowPromiseService(PromiseServiceTypeEnum::SERVICE)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::SERVICE;
        }
        // 保价
        if ($this->isAllowPromiseService(PromiseServiceTypeEnum::GUARANTEE)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::GUARANTEE;
        }
        // 补发
        if (in_array($this->shipping_status, [ ShippingStatusEnum::PART_SHIPPED, ShippingStatusEnum::SHIPPED ], true)) {
            $allowApplyRefundTypes[] = RefundTypeEnum::RESHIPMENT;
        }


        return $allowApplyRefundTypes;
    }


    public function isAllowPromiseService(PromiseServiceTypeEnum $type) : bool
    {
        /**
         * @var $promiseService PromiseServiceValue
         */
        $promiseService = $this->promise_services->{$type->value};

        // 判断固定类型的
        if ($promiseService->isEnum()) {
            if ($promiseService->value() === PromiseServiceValue::UNSUPPORTED) {
                return false;
            }
            if (($promiseService->value() === PromiseServiceValue::BEFORE_SHIPMENT)
                && in_array($this->shipping_status, [ ShippingStatusEnum::NIL, ShippingStatusEnum::READY_SEND, ShippingStatusEnum::WAIT_SEND ], true)) {
                return true;
            }
            return false;
        }

        // 时间类判断
        $lastTime = now();
        switch ($type) {
            case PromiseServiceTypeEnum::EXCHANGE: // 换货
            case PromiseServiceTypeEnum::SERVICE: // 保修
            case PromiseServiceTypeEnum::REFUND: // 退款
                $lastTime = $this->confirm_time ?? now();
                break;
            case PromiseServiceTypeEnum::GUARANTEE: // 保价
                $lastTime = $this->created_time ?? now();
                break;
        }
        // 添加时长
        $lastTime->add($promiseService->value());
        // 判断是否超过了时间
        if (now()->diffInRealSeconds($lastTime, false) > 0) {
            return true;
        } else {
            return false;
        }

    }


}
