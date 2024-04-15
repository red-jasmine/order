<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Domain\Repositories\RefundRepositoryInterface;

abstract class AbstractRefundCommandHandler
{

    public function __construct(protected RefundRepositoryInterface $refundRepository)
    {

    }

}
