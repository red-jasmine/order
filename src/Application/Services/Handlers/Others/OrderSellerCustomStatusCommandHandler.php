<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderSellerCustomStatusCommand;

class OrderSellerCustomStatusCommandHandler extends AbstractOrderCommandHandler
{

    public function execute(OrderSellerCustomStatusCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $order->setSellerCustomStatus($command->sellerCustomStatus, $command->orderProductId);

        $this->orderRepository->update($order);

    }
}
