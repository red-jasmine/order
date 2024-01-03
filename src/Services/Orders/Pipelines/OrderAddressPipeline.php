<?php

namespace RedJasmine\Order\Services\Orders\Pipelines;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderAddress;

class OrderAddressPipeline
{

    public function handle(Order $order, \Closure $next)
    {
        $parameters = $order->getParameters();
        $address    = $parameters['address'] ?? null;

        $order = $next($order);
        $order->setRelation('address', OrderAddress::make($address));
        $order->address()->save($order->address);
        return $order;
    }

}
