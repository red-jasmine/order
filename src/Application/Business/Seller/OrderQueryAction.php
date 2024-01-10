<?php

namespace RedJasmine\Order\Application\Business\Seller;


use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryAction extends \RedJasmine\Order\Services\Orders\OrderQueryAction
{
    public function query() : QueryBuilder
    {
        $query = parent::query();
        $query->onlySeller($this->service->getOwner());
        return $query;
    }


}
