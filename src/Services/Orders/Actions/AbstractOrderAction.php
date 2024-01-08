<?php

namespace RedJasmine\Order\Services\Orders\Actions;

use RedJasmine\Support\Traits\Services\ServiceExtends;

class AbstractOrderAction
{
    use ServiceExtends;

    protected $service;

    public function setService($service) : static
    {
        $this->service  = $service;
        return $this;
    }


}
