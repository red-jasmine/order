<?php

namespace RedJasmine\Order\Domain\Repositories;

use RedJasmine\Order\Domain\Models\OrderRefund;

interface RefundRepositoryInterface
{

    public function find(int $rid) : OrderRefund;


    public function store(OrderRefund $orderRefund) : void;


    public function update(OrderRefund $orderRefund) : void;
}
