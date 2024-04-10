<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers;

use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderConfirmCommand;

class OrderConfirmCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(OrderConfirmCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $order->confirm();

        $this->orderRepository->update($order);

    }


}
