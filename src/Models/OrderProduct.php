<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\ShipStatusEnum;
use RedJasmine\Order\Enums\Orders\ShipTypeEnum;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\WithDTO;

class OrderProduct extends Model
{

    use WithDTO;

    use HasDateTimeFormatter;

    use SoftDeletes;


    public $incrementing = false;


    protected $casts = [
        'ship_type'   => ShipTypeEnum::class,
        'order_status'    => OrderStatusEnum::class,
        'ship_status' => ShipStatusEnum::class,
        'payment_status'  => PaymentStatusEnum::class,
    ];

    protected $fillable = [
        'ship_type',
        'product_type',
        'product_id',
        'sku_id',
        'num',
        'price',
    ];


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'oid', 'id');
    }


    public function info() : HasOne
    {
        return $this->hasOne(OrderProductInfo::class, 'id', 'id');
    }

}
