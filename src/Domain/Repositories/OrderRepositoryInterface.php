<?php

namespace RedJasmine\Order\Domain\Repositories;


use RedJasmine\Order\Domain\Models\Order;

interface OrderRepositoryInterface
{
    public function find(int $id) : Order;

    public function store(Order $order) : Order;

    public function update(Order $order) : void;

}
