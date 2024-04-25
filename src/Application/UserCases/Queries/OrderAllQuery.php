<?php

namespace RedJasmine\Order\Application\UserCases\Queries;

use RedJasmine\Support\Data\Data;

class OrderAllQuery extends Data
{
    // TODO 支持的条件
    public function __construct(public array $query = [])
    {
    }
}
