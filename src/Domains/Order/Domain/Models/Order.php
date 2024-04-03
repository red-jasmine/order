<?php

namespace RedJasmine\Order\Domains\Order\Domain\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Support\Casts\AesEncrypted;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Data\UserData;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use Spatie\LaravelData\WithData;

class Order extends Model
{

    // TODO  has $events

    use WithData;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;

    public bool $withTradePartiesNickname = true;

    public $incrementing = false;


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
        'is_buyer_delete'  => 'boolean'
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

    public function guide() : Attribute
    {
        return Attribute::make(
            get: static fn(mixed $value, array $attributes) => UserData::from([
                                                                                  'type' => $attributes['guide_type'],
                                                                                  'id'   => $attributes['guide_id'],
                                                                              ]),
            set: static fn(?UserInterface $user) => [
                'guide_type' => $user?->getType(),
                'guide_id'   => $user?->getID(),
            ]

        );
    }


    public function addProduct(OrderProduct $orderProduct) : static
    {
        $this->products->add($orderProduct);
        return $this;
    }


    public function calculateAmount() : static
    {
        return $this;
    }


    public function create()
    {
        // 计算金额
        $this->calculateAmount();


        event();
    }
}
