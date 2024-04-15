<?php

namespace RedJasmine\Order\Application\Services;

use RedJasmine\Order\Application\Services\Handlers\Refund\RefundCreateCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;

class RefundService
{


    /**
     * 创建
     *
     * @param RefundCreateCommand $command
     *
     * @return int
     */
    public function create(RefundCreateCommand $command) : int
    {
        return app(RefundCreateCommandHandler::class)->execute($command);
    }


    public function agree()
    {

    }

}
