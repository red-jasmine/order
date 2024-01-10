<?php

namespace RedJasmine\Order\Application\Business\Seller;

class OrderService extends \RedJasmine\Order\OrderService
{
    public function queries() : OrderQueryAction
    {
        return app(OrderQueryAction::class)->setService($this);
    }


}
