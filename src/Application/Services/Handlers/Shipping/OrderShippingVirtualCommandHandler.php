<?php

namespace RedJasmine\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Services\OrderShippingService;

class OrderShippingVirtualCommandHandler extends AbstractOrderCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {

        parent::__construct($orderRepository);
    }


    public function execute(OrderShippingVirtualCommand $command) : void
    {

        $order = $this->find($command->id);

        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $this->orderShippingService->virtual($order, $command->orderProductId, $command->isPartShipped));
        $this->orderRepository->update($order);
        $this->pipelineManager()->call('executed');

    }


}
