<?php

namespace RedJasmine\Order\Application\Order;

use RedJasmine\Order\Application\Order\Data\Commands\OrderCreateData;
use RedJasmine\Order\Application\Order\Data\OrderData;
use RedJasmine\Order\Application\Order\UserCases\Commands\OrderCreateCommand;

class OrderService implements OrderServiceInterface
{
    // has Commands
    // has queries


    public function create(OrderData $data) : OrderData
    {
        return app()->make(OrderCreateCommand::class)->execute($data);
    }


}
