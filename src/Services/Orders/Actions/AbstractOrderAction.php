<?php

namespace RedJasmine\Order\Services\Orders\Actions;

use RedJasmine\Order\OrderService;
use RedJasmine\Support\Traits\Services\ServiceExtends;

class AbstractOrderAction
{
    use ServiceExtends;

    protected ?OrderService $service;

    public function setService($service) : static
    {
        $this->service = $service;
        return $this;
    }


}
