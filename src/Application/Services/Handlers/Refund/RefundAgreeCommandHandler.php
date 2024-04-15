<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Domain\Exceptions\RefundException;

class RefundAgreeCommandHandler extends AbstractRefundCommandHandler
{


    /**
     * @param RefundAgreeCommand $command
     *
     * @return void
     * @throws RefundException
     */
    public function execute(RefundAgreeCommand $command) : void
    {
        $refund = $this->refundRepository->find($command->rid);

        $refund->agree($command->amount);

        $this->refundRepository->update($refund);

    }

}
