<?php

namespace RedJasmine\Order\Services\Order\Data\Shipping;

use RedJasmine\Order\Services\Order\Enums\OrderCardKeyStatusEnum;
use RedJasmine\Support\Data\Data;

class OrderCardKeyContentData extends Data
{
    public string $content;

    public OrderCardKeyStatusEnum $status = OrderCardKeyStatusEnum::SHIPPED;

    /**
     * 扩展字段
     * @var array|null
     */
    public ?array $extends = null;
}
