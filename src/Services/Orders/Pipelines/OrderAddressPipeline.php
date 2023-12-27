<?php

namespace RedJasmine\Order\Services\Orders\Pipelines;

use RedJasmine\Order\Models\Order;

class OrderAddressPipeline
{

    public function handle(Order $order, \Closure $next)
    {
        $parameters = $order->getParameters();

        // TODO 验证 判断是需要 地址 如果不存在那么就设置为空
        $address = $parameters['address'] ?? null;

        $order = $next($order);
        // 地址合集
        //
        $order->address()->save($address);

        return $order;
    }

}
