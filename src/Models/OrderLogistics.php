<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Enums\Logistics\LogisticsStatusEnum;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;

class OrderLogistics extends Model
{

    use HasDateTimeFormatter;

    use HasTradeParties;

    use HasOperator;

    use SoftDeletes;


    protected $casts = [
        'order_product_id' => 'array',
        'shipper'          => LogisticsShipperEnum::class,
        'status'           => LogisticsStatusEnum::class,
        'extends'          => 'array',
        'shipping_time'    => 'datetime',
        'collect_time'     => 'datetime',
        'dispatch_time'    => 'datetime',
        'signed_time'      => 'datetime',
    ];

    public function shippable() : MorphTo
    {
        return $this->morphTo();
    }

}
