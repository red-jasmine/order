<?php

namespace RedJasmine\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand;
use RedJasmine\Order\Domain\OrderFactory;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Services\OrderShippingService;

class OrderShippingCardKeyCommandHandler extends AbstractOrderCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {

        parent::__construct($orderRepository);
    }



    public function execute(OrderShippingCardKeyCommand $command) : void
    {

        $order = $this->find($command->id);
        $orderProductCardKey = app(OrderFactory::class)->createOrderProductCardKey();

        $orderProductCardKey->order_product_id = $command->orderProductId;
        $orderProductCardKey->content          = $command->content;
        $orderProductCardKey->extends          = $command->extends;
        $orderProductCardKey->status           = $command->status;


        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $this->orderShippingService->cardKey($order, $orderProductCardKey));
        $this->orderRepository->update($order);
        $this->pipelineManager()->call('executed');

    }


}
