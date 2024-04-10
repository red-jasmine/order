<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand;
use RedJasmine\Order\Domains\Order\Domain\OrderFactory;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domains\Order\Domain\Services\OrderShippingService;

class OrderShippingVirtualCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {
    }


    public function execute(OrderShippingVirtualCommand $command) : void
    {

        $order = $this->orderRepository->find($command->id);

        $this->orderShippingService->virtual($order, $command->orderProductId, $command->isPartShipped);

        $this->orderRepository->update($order);

    }


}
