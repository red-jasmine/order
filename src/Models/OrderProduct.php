<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use RedJasmine\Support\Traits\Models\WithDTO;

class OrderProduct extends Model
{

    use WithDTO;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;


    public $incrementing = false;


    protected $casts = [
        'shipping_type'   => ShippingTypeEnum::class,
        'order_status'    => OrderStatusEnum::class,
        'shipping_status' => ShippingStatusEnum::class,
        'payment_status'  => PaymentStatusEnum::class,
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


}
