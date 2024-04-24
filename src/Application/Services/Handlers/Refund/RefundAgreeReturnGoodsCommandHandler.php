<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeReturnGoodsCommand;

class RefundAgreeReturnGoodsCommandHandler extends AbstractRefundCommandHandler
{




    public function execute(RefundAgreeReturnGoodsCommand $command) : void
    {

        $refund  = $this->find($command->rid);

        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->agreeReturnGoods());
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');
    }

}
