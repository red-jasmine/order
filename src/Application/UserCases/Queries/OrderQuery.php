<?php

namespace RedJasmine\Order\Application\UserCases\Queries;

use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Support\Foundation\Service\HasQueryBuilder;

class OrderQuery
{


    use HasQueryBuilder;


    protected string $model = Order::class;


}
