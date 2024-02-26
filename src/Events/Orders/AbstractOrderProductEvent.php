<?php

namespace RedJasmine\Order\Events\Orders;

use Illuminate\Foundation\Events\Dispatchable;
use RedJasmine\Order\Models\OrderProduct;

abstract class AbstractOrderProductEvent
{
    use Dispatchable;


    protected OrderProduct $orderProduct;

    public function __construct(OrderProduct $orderProduct)
    {
        $this->orderProduct = $orderProduct;
    }

}
