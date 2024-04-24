<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectReturnGoodsCommand;

class RefundRejectReturnGoodsCommandHandler extends AbstractRefundCommandHandler
{



    public function execute(RefundRejectReturnGoodsCommand $command) : void
    {

        $refund  = $this->find($command->rid);
        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->rejectReturnGoods($command->reason));
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');

    }

}
