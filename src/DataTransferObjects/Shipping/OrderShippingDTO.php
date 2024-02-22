<?php

namespace RedJasmine\Order\DataTransferObjects\Shipping;

use RedJasmine\Support\DataTransferObjects\Data;

class OrderShippingDTO extends Data
{
    /**
     * 是否拆分发货
     * @var bool
     */
    public bool $isSplit = false;

    /**
     * 部分订单商品 集合
     * @var array|null
     */
    public ?array $orderProducts = null;
}
