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
use RedJasmine\Support\Application\ApplicationService;

/**
 * @method int create(RefundCreateCommand $command)
 * @method void agree(RefundAgreeCommand $command)
 * @method void reject(RefundRejectCommand $command)
 * @method void cancel(RefundCancelCommand $command)
 * @method void agreeReturnGoods(RefundAgreeReturnGoodsCommand $command)
 * @method void returnGoods(RefundReturnGoodsCommand $command)
 * @method void rejectReturnGoods(RefundRejectReturnGoodsCommand $command)
 * @method void reshipGoods(RefundReshipGoodsCommand $command)
 */
class RefundCommandService extends ApplicationService
{
    protected static $macros = [
        'create'            => RefundCreateCommandHandler::class,
        'agree'             => RefundAgreeCommandHandler::class,
        'reject'            => RefundRejectCommandHandler::class,
        'cancel'            => RefundCancelCommandHandler::class,
        'agreeReturnGoods'  => RefundAgreeReturnGoodsCommandHandler::class,
        'returnGoods'       => RefundReturnGoodsCommandHandler::class,
        'rejectReturnGoods' => RefundRejectReturnGoodsCommandHandler::class,
        'reshipGoods'       => RefundReshipGoodsCommandHandler::class,
    ];

}
