<?php

namespace RedJasmine\Order;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Order\Services\Orders\OrderQueryService;
use RedJasmine\Support\Traits\Services\WithUserService;

class OrderService
{

    use WithUserService;


    /**
     * @param int $id
     *
     * @return Order
     */
    public function find(int $id) : Order
    {
        return Order::findOrFail($id);
    }

    public function queries() : OrderQueryService
    {
        return new OrderQueryService($this);
    }

    public function creator() : OrderCreatorService
    {
        return new OrderCreatorService($this);
    }


}
