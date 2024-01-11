<?php

namespace RedJasmine\Order\Pipelines;

use RedJasmine\Order\DataTransferObjects\OrderDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderAddress;

class OrderAddressPipeline
{

    public function handle(Order $order, \Closure $next)
    {

        /**
         *
         * @var $orderDTO OrderDTO
         */
        $orderDTO = $order->getDTO();


        $order = $next($order);
        if (filled($orderDTO->address)) {
            $order->setRelation('address', OrderAddress::make($orderDTO->address->toArray()));
            $order->address()->save($order->address);
        }
        return $order;
    }

}
