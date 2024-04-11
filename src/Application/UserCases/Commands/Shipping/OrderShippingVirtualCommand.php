<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Shipping;

use RedJasmine\Support\Data\Data;

class OrderShippingVirtualCommand extends Data
{
    public int $id;

    public int $orderProductId;

    /**
     * 是否部分发货
     * @var bool
     */
    public bool $isPartShipped = false;
}
