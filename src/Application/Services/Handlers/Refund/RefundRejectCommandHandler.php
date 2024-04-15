<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;

class RefundRejectCommandHandler extends AbstractRefundCommandHandler
{


    public function execute(RefundRejectCommand $command) : void
    {
        $refund = $this->refundRepository->find($command->rid);


        $refund->reject($command->reason);


        $this->refundRepository->update($refund);
    }

}
