<?php

namespace RedJasmine\Order\Application\Services;

use RedJasmine\Order\Application\Services\Handlers\Refund\RefundAgreeCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundAgreeReturnGoodsCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundCancelCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundCreateCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundRejectCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundRejectReturnGoodsCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundReshipGoodsCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Refund\RefundReturnGoodsCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeReturnGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCancelCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectReturnGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReshipGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReturnGoodsCommand;
use RedJasmine\Order\Domain\Exceptions\RefundException;

class RefundService extends ApplicationService
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


    public function cancel(RefundCancelCommand $command) : void
    {
        app(RefundCancelCommandHandler::class)->execute($command);
    }


    public function agreeReturnGoods(RefundAgreeReturnGoodsCommand $command) : void
    {
        app(RefundAgreeReturnGoodsCommandHandler::class)->execute($command);
    }


    public function returnGoods(RefundReturnGoodsCommand $command) : void
    {
        app(RefundReturnGoodsCommandHandler::class)->execute($command);
    }


    public function rejectReturnGoods(RefundRejectReturnGoodsCommand $command) : void
    {
        app(RefundRejectReturnGoodsCommandHandler::class)->execute($command);
    }


    public function reshipGoods(RefundReshipGoodsCommand $command) : void
    {
        app(RefundReshipGoodsCommandHandler::class)->execute($command);
    }

}
