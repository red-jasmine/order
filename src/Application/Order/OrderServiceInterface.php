<?php

namespace RedJasmine\Order\Application\Order;

use RedJasmine\Order\Application\Order\Data\Commands\OrderCreateData;
use RedJasmine\Order\Application\Order\Data\OrderData;

/**
 * defining user case
 */
interface OrderServiceInterface
{
    // 待定
    public function create(OrderCreateData $data) : OrderData;

}
