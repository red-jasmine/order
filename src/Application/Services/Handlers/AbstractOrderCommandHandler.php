<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\Services\CommandHandler;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;


abstract class AbstractOrderCommandHandler extends CommandHandler
{

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }
}
