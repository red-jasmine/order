<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers;

use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;

abstract class AbstractOrderCommandHandler
{

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }
}
