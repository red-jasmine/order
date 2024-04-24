<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\Services\CommandHandler;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Order\Domain\Repositories\RefundRepositoryInterface;

abstract class AbstractRefundCommandHandler extends CommandHandler
{


    protected ?OrderRefund $model = null;

    public function __construct(protected RefundRepositoryInterface $refundRepository)
    {

    }


    protected function find(int $id) : OrderRefund
    {
        $refund = $this->refundRepository->find($id);
        $this->setModel($refund);

        return $refund;
    }


}
