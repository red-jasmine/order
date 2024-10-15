<?php

namespace RedJasmine\Order\Domain\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Support\Facades\Log;
use RedJasmine\Order\Domain\Models\Order;

class OrderPayingEvent extends AbstractOrderEvent implements ShouldDispatchAfterCommit
{

    public function __construct(
        public readonly Order $order
    ) {
        Log::info('OrderPayingEvent---');
    }

}
