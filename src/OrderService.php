<?php

namespace RedJasmine\Order;

use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Support\Traits\Services\WithUserService;

class OrderService
{

    use WithUserService;


    public function creator() : OrderCreatorService
    {
        return new OrderCreatorService($this);
    }


}
