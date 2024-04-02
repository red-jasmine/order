<?php

namespace RedJasmine\Order\Application\Order\Repositories\Eloquent;

use RedJasmine\Order\Domain\Order\Models\Order;
use RedJasmine\Order\Domain\Order\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    // 仓库层返回的应该是 领域模型
    public function store(Order $order) : Order
    {

        $order->push();
        return $order;
    }


}
