<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCancelCommand;

class RefundCancelCommandHandler extends AbstractRefundCommandHandler
{



    public function execute(RefundCancelCommand $command) : void
    {

        $refund  = $this->find($command->rid);
        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->cancel());
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');
    }

}
