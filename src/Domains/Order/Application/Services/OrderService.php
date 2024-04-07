<?php

namespace RedJasmine\Order\Domains\Order\Application\Services;


use RedJasmine\Order\Domains\Order\Application\Data\OrderData;
use RedJasmine\Order\Domains\Order\Application\Services\Handlers\OrderCreateCommandHandler;
use RedJasmine\Order\Domains\Order\Application\Services\Handlers\OrderPaidCommandHandler;
use RedJasmine\Order\Domains\Order\Application\Services\Handlers\OrderPayingCommandHandler;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPayingCommand;


class OrderService
{
    // TODO 可扩展

    public function create(OrderCreateCommand $command) : OrderData
    {
        return app(OrderCreateCommandHandler::class)->execute($command);
    }

    public function paying(OrderPayingCommand $command) : int
    {
        return app(OrderPayingCommandHandler::class)->execute($command);
    }

    public function paid(OrderPaidCommand $command) : bool
    {
        return app(OrderPaidCommandHandler::class)->execute($command);
    }

}
