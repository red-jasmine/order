<?php

namespace RedJasmine\Order\Domains\Order\Application\Services;

use RedJasmine\Order\Domains\Order\Application\Services\Handlers\OrderCreateCommandHandler;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCreateCommand;


class OrderService
{

    public function create(OrderCreateCommand $command)
    {
        return app(OrderCreateCommandHandler::class)->execute($command);
    }

}
