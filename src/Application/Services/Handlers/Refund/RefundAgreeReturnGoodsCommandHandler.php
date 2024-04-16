<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeReturnGoodsCommand;

class RefundAgreeReturnGoodsCommandHandler extends AbstractRefundCommandHandler
{


    public function execute(RefundAgreeReturnGoodsCommand $command) : void
    {
        $refund = $this->refundRepository->find($command->rid);

        $refund->agreeReturnGoods();

        $this->refundRepository->update($refund);
    }

}
