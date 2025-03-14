<?php

namespace RedJasmine\Order\Application\Services\Payments;

use RedJasmine\Order\Domain\Repositories\OrderPaymentReadRepositoryInterface;
use RedJasmine\Support\Application\ApplicationQueryService;

class OrderPaymentQueryService extends ApplicationQueryService
{
    public function __construct(protected OrderPaymentReadRepositoryInterface $repository)
    {

    }
}
