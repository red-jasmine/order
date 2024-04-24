<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;

class RefundRejectCommandHandler extends AbstractRefundCommandHandler
{




    public function execute(RefundRejectCommand $command) : void
    {

        $refund  = $this->find($command->rid);
        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->reject($command->reason));
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');
    }

}
