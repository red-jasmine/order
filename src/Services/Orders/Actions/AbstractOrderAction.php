<?php

namespace RedJasmine\Order\Services\Orders\Actions;

use RedJasmine\Order\OrderService;
use RedJasmine\Support\Foundation\Service\Action;

class AbstractOrderAction extends Action
{
    protected ?OrderService $service;

}
