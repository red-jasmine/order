<?php

namespace RedJasmine\Order\Business\Buyer;

class OrderService extends \RedJasmine\Order\OrderService
{
    public function queries() : OrderQueryService
    {
        return app(OrderQueryService::class)->setService($this);
    }


}
