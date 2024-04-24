<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Domain\Exceptions\RefundException;

class RefundAgreeCommandHandler extends AbstractRefundCommandHandler
{


    public function execute(RefundAgreeCommand $command) : void
    {

        $refund = $this->find($command->rid);

        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->agree($command->amount));
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');
    }

}
