<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Orders\OrderStatusEnums;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnums;
use RedJasmine\Order\Enums\Orders\RefundStatusEnums;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnums;
use RedJasmine\Order\Enums\Orders\OrderTypeEnums;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\ParametersMakeAble;

class Order extends Model
{

    use HasDateTimeFormatter;

    use SoftDeletes;

    use ParametersMakeAble;

    public $incrementing = false;

    protected $fillable = [
        'order_type',
        'shipping_type',
    ];

    protected $casts = [
        'order_type'      => OrderTypeEnums::class,
        'shipping_type'   => ShippingTypeEnums::class,
        'order_status'    => OrderStatusEnums::class,
        'payment_status'  => PaymentStatusEnums::class,
        'shipping_status' => ShippingStatusEnums::class,
        'refund_status'   => RefundStatusEnums::class,
        'created_time'    => 'datetime',
        'payment_time'    => 'datetime',
        'close_time'      => 'datetime',
        'consign_time'    => 'datetime',
        'collect_time'    => 'datetime',
        'dispatch_time'   => 'datetime',
        'signed_time'     => 'datetime',
        'end_time'        => 'datetime',
        'refund_time'     => 'datetime',
        'rate_time'       => 'datetime',
    ];


    public function info() : HasOne
    {
        return $this->hasOne(OrderInfo::class, 'id', 'id');
    }

    public function products() : HasMany
    {
        return $this->hasMany(OrderProduct::class, 'oid', 'id');
    }
}
