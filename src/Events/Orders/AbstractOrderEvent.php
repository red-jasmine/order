<?php

namespace RedJasmine\Order\Events\Orders;

use Illuminate\Foundation\Events\Dispatchable;
use RedJasmine\Order\Models\Order;

abstract class AbstractOrderEvent
{
    use Dispatchable;


    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
