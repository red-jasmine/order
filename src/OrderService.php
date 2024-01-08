<?php

namespace RedJasmine\Order;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Order\Services\Orders\OrderPayAction;
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
        return app(OrderQueryService::class)->setService($this);
    }

    public function creator() : OrderCreatorService
    {
        return app(OrderCreatorService::class)->setService($this);
    }


    public function pay() : OrderPayAction
    {
        return app(OrderPayAction::class)->setService($this);
    }


}
