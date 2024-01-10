<?php

namespace RedJasmine\Order;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\Actions\OrderPayingAction;
use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Order\Services\Orders\OrderQueryService;
use RedJasmine\Support\Foundation\Service\Service;

/**
 * @method OrderPayingAction paying()
 */
class OrderService extends Service
{


    protected static string $actionsConfigKey = 'red-jasmine.order.actions';

    /**
     * @param int $id
     *
     * @return Order
     */
    public function find(int $id) : Order
    {
        return Order::findOrFail($id);
    }

    public function findLock(int $id) : Order
    {
        return Order::lockForUpdate()->findOrFail($id);
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
