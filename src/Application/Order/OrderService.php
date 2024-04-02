<?php

namespace RedJasmine\Order\Application\Order;

use RedJasmine\Order\Application\Order\Data\Commands\OrderCreateData;
use RedJasmine\Order\Application\Order\Data\OrderData;
use RedJasmine\Order\Application\Order\UserCases\Commands\OrderCreateCommand;

class OrderService implements OrderServiceInterface
{
    public function create(OrderCreateData $data) : OrderData
    {
        return app()->make(OrderCreateCommand::class)->execute($data);
    }


}
