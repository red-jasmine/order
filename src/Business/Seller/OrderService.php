<?php

namespace RedJasmine\Order\Business\Seller;

class OrderService extends \RedJasmine\Order\OrderService
{
    public function queries() : OrderQueryService
    {
        return new OrderQueryService($this);
    }


}
