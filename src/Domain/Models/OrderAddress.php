<?php

namespace RedJasmine\Order\Domain\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Support\Domain\Models\Traits\HasOperator;
use RedJasmine\Support\Domain\Models\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Domain\Models\Traits\HasSnowflakeId;

class OrderAddress extends Model
{

    use HasSnowflakeId;

    use SoftDeletes;
    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;


    public $incrementing = false;

    public function getTable() : string
    {
        return config('red-jasmine-order.tables.prefix', 'jasmine_') . 'order_addresses';
    }


    protected $fillable = [
        'contacts',
        'mobile',
        'country',
        'province',
        'city',
        'district',
        'street',
        'country_id',
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'address',
        'zip_code',
        'lon',
        'lat',
    ];

    protected $casts = [
        'extras'  => 'array',
        'contacts' => 'encrypted',
        'mobile'   => 'encrypted',
        'address'  => 'encrypted',
    ];

    protected $appends = [
        'full_address'
    ];


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'id', 'id');
    }

    public function fullAddress() : Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => implode([ $attributes['province'], $attributes['city'], $attributes['district'], $attributes['street'], $attributes['address'] ])
        );

    }
}
