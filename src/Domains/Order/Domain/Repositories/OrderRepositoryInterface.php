<?php

namespace RedJasmine\Order\Domains\Order\Domain\Repositories;


use RedJasmine\Order\Domains\Order\Domain\Models\Order;

interface OrderRepositoryInterface
{
    public function find(int $id) : Order;

    public function store(Order $order) : Order;

    public function update(Order $order) : void;

}
