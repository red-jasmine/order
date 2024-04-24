<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderConfirmCommand;


class OrderConfirmCommandHandler extends AbstractOrderCommandHandler
{


    /**
     * @param OrderConfirmCommand $command
     *
     * @return void
     */
    public function execute(OrderConfirmCommand $command) : void
    {

        $order = $this->find($command->id);

        $this->handle(
            execute: fn() => $order->confirm(),
            persistence: fn() => $this->orderRepository->update($order)
        );
    }


}
