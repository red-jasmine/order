<?php

namespace RedJasmine\Order\Domains\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domains\Common\Domain\Models\HasTradeParties;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderProductTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundGoodsStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundPhaseEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;


class OrderRefund extends Model
{
    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;

    public $incrementing = false;

    protected $casts = [
        'order_product_type' => OrderProductTypeEnum::class,
        'shipping_type'      => ShippingTypeEnum::class,
        'refund_type'        => RefundTypeEnum::class,
        'refund_status'      => RefundStatusEnum::class,
        'good_status'        => RefundGoodsStatusEnum::class,
        'phase'              => RefundPhaseEnum::class,
        'has_good_return'    => 'boolean',
        'end_time'           => 'datetime',
        'images'             => 'array',
        'extends'            => 'array',
    ];


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderProduct() : BelongsTo
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id', 'id');
    }


    public function logistics() : MorphMany
    {
        return $this->morphMany(OrderLogistics::class, 'shippable');
    }


}
