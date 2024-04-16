<?php

namespace RedJasmine\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RedJasmine\Order\Domain\Enums\Payments\AmountTypeEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Models\Order;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;

/**
 * 订单支付
 */
class OrderPayment extends Model
{

    public $incrementing = false;

    use HasDateTimeFormatter;

    use HasOperator;

    use HasTradeParties;

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    protected $casts = [
        'amount_type' => AmountTypeEnum::class,
        'status'      => PaymentStatusEnum::class,
    ];
}
