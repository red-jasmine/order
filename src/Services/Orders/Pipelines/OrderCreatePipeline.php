<?php

namespace RedJasmine\Order\Services\Orders\Pipelines;

use Closure;
use RedJasmine\Order\Models\Order;

class OrderCreatePipeline
{


    public function handle(Order $order, Closure $next)
    {
        return $next($order);
    }
}
