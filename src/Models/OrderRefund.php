<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Orders\OrderProductTypeEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Order\Enums\Refund\RefundGoodsStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use RedJasmine\Support\Traits\Models\WithDTO;

class OrderRefund extends Model
{
    use WithDTO;

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

}
