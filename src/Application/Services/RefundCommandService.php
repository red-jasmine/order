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
use RedJasmine\Support\Application\ApplicationService;

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
