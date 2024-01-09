<?php

namespace RedJasmine\Order;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Order\Services\Orders\OrderQueryService;
use RedJasmine\Support\Foundation\Service\Service;

/**
 * @method bool pay(int $id)
 */
class OrderService extends Service
{

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


}
