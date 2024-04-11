<?php

namespace RedJasmine\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Domain\OrderFactory;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Services\OrderShippingService;

class OrderShippingCardKeyCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {
    }


    public function execute(\RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand $command) : void
    {

        $order = $this->orderRepository->find($command->id);

        $orderProductCardKey = app(OrderFactory::class)->createOrderProductCardKey();

        $orderProductCardKey->order_product_id = $command->orderProductId;
        $orderProductCardKey->content          = $command->content;
        $orderProductCardKey->extends          = $command->extends;
        $orderProductCardKey->status           = $command->status;

        $this->orderShippingService->cardKey($order, $orderProductCardKey);

        $this->orderRepository->update($order);

    }


}
