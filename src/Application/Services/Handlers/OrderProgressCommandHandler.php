<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderProgressCommand;

class OrderProgressCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(OrderProgressCommand $command) : int
    {
        $order = $this->find($command->id);
        return $this->handle(
            execute: fn() => $order->setProductProgress($command->orderProductId, $command->progress, $command->isAbsolute, $command->isAllowLess),
            persistence: fn() => $this->orderRepository->store($order)
        );

    }

}
