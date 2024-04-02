<?php

namespace RedJasmine\Order\Domain\Order;

use RedJasmine\Order\Domain\Order\Models\Order;

interface OrderRepositoryInterface
{


    public function store(Order $order):Order;
}
