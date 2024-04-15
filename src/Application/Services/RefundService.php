<?php

namespace RedJasmine\Order\Application\Services;

use RedJasmine\Order\Application\Services\Handlers\Refund\RefundAgreeCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundCreateCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundRejectCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;
use RedJasmine\Order\Domain\Exceptions\RefundException;

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


    /**
     * @param RefundAgreeCommand $command
     *
     * @return void
     * @throws RefundException
     */
    public function agree(RefundAgreeCommand $command) : void
    {
        app(RefundAgreeCommandHandler::class)->execute($command);
    }


    public function reject(RefundRejectCommand $command) : void
    {
        app(RefundRejectCommandHandler::class)->execute($command);
    }

}
