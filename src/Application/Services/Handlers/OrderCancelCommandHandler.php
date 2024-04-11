<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderCancelCommand;

class OrderCancelCommandHandler extends AbstractOrderCommandHandler
{

    public function execute(OrderCancelCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $order->cancel($command->cancelReason);

        $this->orderRepository->update($order);



    }

}
