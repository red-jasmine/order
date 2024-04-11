<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;

abstract class AbstractOrderCommandHandler
{

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }
}
