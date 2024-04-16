<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCancelCommand;

class RefundCancelCommandHandler extends AbstractRefundCommandHandler
{


    public function execute(RefundCancelCommand $command) : void
    {
        $refund = $this->refundRepository->find($command->rid);

        $refund->cancel();

        $this->refundRepository->update($refund);
    }

}
