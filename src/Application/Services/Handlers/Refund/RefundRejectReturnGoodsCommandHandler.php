<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectReturnGoodsCommand;

class RefundRejectReturnGoodsCommandHandler extends AbstractRefundCommandHandler
{

    public function execute(RefundRejectReturnGoodsCommand $command):void
    {

        $refund = $this->refundRepository->find($command->rid);

        $refund->rejectReturnGoods($command->reason);


        $this->refundRepository->update($refund);

    }

}
