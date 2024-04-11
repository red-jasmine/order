<?php

namespace RedJasmine\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Services\OrderShippingService;

class OrderShippingVirtualCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {
    }


    public function execute(\RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand $command) : void
    {

        $order = $this->orderRepository->find($command->id);

        $this->orderShippingService->virtual($order, $command->orderProductId, $command->isPartShipped);

        $this->orderRepository->update($order);

    }


}
