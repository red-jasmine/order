<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Domain\OrderFactory;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;

class OrderPayingCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(OrderPayingCommand $command) : OrderPayment
    {
        $order        = $this->find($command->id);
        $orderPayment = app(OrderFactory::class)->createOrderPayment();

        $orderPayment->payment_amount = $command->amount;
        $orderPayment->amount_type    = $command->amountType;


        $this->handle(
            execute: fn() => $order->paying($orderPayment),
            persistence: fn() => $this->orderRepository->store($order)
        );
        return $orderPayment;
    }

}
