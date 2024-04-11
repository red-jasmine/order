<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderProgressCommand;
use RedJasmine\Order\Domain\Models\ValueObjects\Progress;

class OrderProgressCommandHandler extends AbstractOrderCommandHandler
{

    public function execute(OrderProgressCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $progress = Progress::from([ 'progress' => $command->progress, 'progress_total' => $command->progressTotal, ]);

        $order->setProductProgress($command->orderProductId, $progress);

        $this->orderRepository->update($order);
    }
}
