<?php

namespace RedJasmine\Order\Domains\Order\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use RedJasmine\Order\Domains\Order\Domain\Models\Order;

/**
 * 领域事件
 */
abstract class AbstractOrderEvent
{
    use Dispatchable;

    public function __construct(
        public Order $order
    )
    {
    }
}
