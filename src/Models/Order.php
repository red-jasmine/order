<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\OrderTypeEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Helpers\UserObjectBuilder;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\ParametersMakeAble;
use RedJasmine\Support\Traits\Models\WithOperatorModel;

class Order extends Model
{

    use HasDateTimeFormatter;

    use SoftDeletes;

    use ParametersMakeAble;

    use WithOperatorModel;

    public $incrementing = false;

    protected $fillable = [
        'order_type',
        'shipping_type',
    ];

    protected $casts = [
        'order_type'      => OrderTypeEnum::class,
        'shipping_type'   => ShippingTypeEnum::class,
        'order_status'    => OrderStatusEnum::class,
        'payment_status'  => PaymentStatusEnum::class,
        'shipping_status' => ShippingStatusEnum::class,
        'refund_status'   => RefundStatusEnum::class,
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

    public function address() : HasOne
    {
        return $this->hasOne(OrderAddress::class, 'id', 'id');
    }

    public function getSeller() : UserInterface
    {
        return new UserObjectBuilder([ 'type' => $this->seller_type, 'id' => $this->seller_id ]);
    }

    public function scopeSeller(Builder $query, UserInterface $owner) : Builder
    {
        return $query->where('seller_type', $owner->getType())
                     ->where('seller_id', $owner->getID());

    }

    public function withSeller(?UserInterface $owner) : void
    {
        if ($owner) {
            $this->seller_type = $owner->getType();
            $this->seller_id   = $owner->getID();
        }
    }

    public function getBuyer() : UserInterface
    {
        return new UserObjectBuilder([ 'type' => $this->buyer_type, 'id' => $this->buyer_id ]);
    }

    public function scopeBuyer(Builder $query, UserInterface $owner) : Builder
    {
        return $query->where('buyer_type', $owner->getType())
                     ->where('buyer_id', $owner->getID());

    }

    public function withBuyer(?UserInterface $owner) : void
    {
        if ($owner) {
            $this->buyer_type = $owner->getType();
            $this->buyer_id   = $owner->getID();
        }
    }

}
