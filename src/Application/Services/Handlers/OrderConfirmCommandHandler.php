<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderConfirmCommand;

class OrderConfirmCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(OrderConfirmCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $order->confirm();

        $this->orderRepository->update($order);

    }


}
