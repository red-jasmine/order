<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers;

use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domains\Order\Domain\OrderFactory;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;

class OrderPayingCommandHandler
{

    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }


    public function execute(OrderPayingCommand $command) : int
    {
        $order = $this->orderRepository->find($command->id);

        $orderPayment                 = app(OrderFactory::class)->createOrderPayment();

        $orderPayment->payment_amount = $command->amount;

        $order->paying($orderPayment);

        $this->orderRepository->update($order);

        $order->dispatchEvents();

        return $orderPayment->id;
    }

}
