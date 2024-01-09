<?php

namespace RedJasmine\Order\Business\Buyer;


use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryService extends \RedJasmine\Order\Services\Orders\OrderQueryService
{
    public function query() : QueryBuilder
    {
        $query = parent::query();
        $query->onlyBuyer($this->service->getOwner());
        return $query;
    }


}
