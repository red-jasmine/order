<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;

class RefundRejectCommandHandler extends AbstractRefundCommandHandler
{


    public function handle(RefundRejectCommand $command) : void
    {

        $refund          = $this->find($command->rid);
        $refund->updater = $this->getOperator();
        $this->execute(
            execute: fn() => $refund->reject($command->reason),
            persistence: fn() => $this->refundRepository->update($refund)
        );
    }

}
