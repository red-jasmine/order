<?php

namespace RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping;

use RedJasmine\Order\Domains\Order\Domain\Enums\OrderCardKeyStatusEnum;
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
