<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Domains\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;

class RefunCreateCommandHandler extends AbstractOrderCommandHandler
{


    public function execute(RefundCreateCommand $command)
    {


        $order = $this->orderRepository->find($command->id);


    }

}
