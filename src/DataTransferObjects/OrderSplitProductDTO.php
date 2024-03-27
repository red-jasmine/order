<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Support\Data\Data;

class OrderSplitProductDTO extends Data
{

    /**
     * 是否拆分处理
     * @var bool
     */
    public bool $isSplit = false;

    /**
     * 部分订单商品 集合
     * @var array|null
     */
    public ?array $orderProducts = null;

}
