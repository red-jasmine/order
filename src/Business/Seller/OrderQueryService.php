<?php

namespace RedJasmine\Order\Business\Seller;


use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryService extends \RedJasmine\Order\Services\Orders\OrderQueryService
{
    public function query() : QueryBuilder
    {
        $query = parent::query();
        $query->owner($this->service->getOwner());
        return $query;
    }


}
