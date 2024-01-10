<?php

namespace RedJasmine\Order\Application\Business\Buyer;


use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryAction extends \RedJasmine\Order\Services\Orders\OrderQueryAction
{
    public function query() : QueryBuilder
    {
        $query = parent::query();
        $query->onlyBuyer($this->service->getOwner());
        return $query;
    }


}
