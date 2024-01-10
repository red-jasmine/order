<?php

namespace RedJasmine\Order\Actions\Shipping;

use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;

/**
 * 虚拟发货
 */
class OrderVirtualShippingAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = null;
}
