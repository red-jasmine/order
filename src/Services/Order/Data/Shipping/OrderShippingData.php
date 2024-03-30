<?php

namespace RedJasmine\Order\Services\Order\Data\Shipping;

use RedJasmine\Support\Data\Data;

class OrderShippingData extends Data
{
    /**
     * 是否拆分
     * @var bool
     */
    public bool $isSplit = false;

    /**
     * 部分订单商品 集合
     * @var array|null
     */
    public ?array $orderProducts = null;
}
