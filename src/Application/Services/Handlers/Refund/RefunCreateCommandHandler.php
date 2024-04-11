<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;

class RefunCreateCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(RefundCreateCommand $command)
    {


        $order = $this->orderRepository->find($command->id);


    }

}
