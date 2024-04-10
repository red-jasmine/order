<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers;

use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;

class OrderPaidCommandHandler
{

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }


    public function execute(OrderPaidCommand $command) : bool
    {
        // 加锁处理
        $order = $this->orderRepository->find($command->id);

        $orderPayment                     = $order->payments->where('id', $command->orderPaymentId)->firstOrFail();
        $orderPayment->payment_amount     = $command->amount;
        $orderPayment->payment_time       = $command->paymentTime;
        $orderPayment->payment_type       = $command->paymentType;
        $orderPayment->payment_id         = $command->paymentId;
        $orderPayment->payment_channel    = $command->paymentChannel;
        $orderPayment->payment_channel_no = $command->paymentChannelNo;
        $orderPayment->payment_method     = $command->paymentMethod;

        // 执行逻辑
        $order->paid($orderPayment);
        // 持久化
        $this->orderRepository->update($order);


        return true;
    }

}
