<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Logistics\LogisticsStatusEnum;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\DataTransferObjects\UserDTO;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;

class OrderLogistics extends Model
{

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;


    protected $casts = [
        'order_product_id' => 'array',
        'status'           => LogisticsStatusEnum::class
    ];


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function scopeOnlySeller(Builder $query, UserInterface $seller) : Builder
    {
        return $query->where('seller_type', $seller->getType())
                     ->where('seller_id', $seller->getID());

    }

    public function seller() : Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => UserDTO::from([
                                                                          'type' => $attributes['seller_type'],
                                                                          'id'   => $attributes['seller_id'],
                                                                      ]),
            set: fn(?UserInterface $user) => [
                'seller_type' => $user?->getType(),
                'seller_id'   => $user?->getID(),
            ]

        );
    }

    public function scopeOnlyBuyer(Builder $query, UserInterface $buyer) : Builder
    {
        return $query->where('buyer_type', $buyer->getType())
                     ->where('buyer_id', $buyer->getID());

    }

    public function buyer() : Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => UserDTO::from([
                                                                          'type' => $attributes['buyer_type'],
                                                                          'id'   => $attributes['buyer_id'],
                                                                      ]),
            set: fn(?UserInterface $user) => [
                'buyer_type' => $user?->getType(),
                'buyer_id'   => $user?->getID(),

            ]

        );
    }
}
