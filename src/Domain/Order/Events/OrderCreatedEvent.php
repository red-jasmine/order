<?php

namespace RedJasmine\Order\Domain\Order\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OrderCreatedEvent
{
    use Dispatchable;

    public function __construct()
    {
    }
}
