<?php

namespace RedJasmine\Order\Actions\Ship;

use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;

/**
 * 虚拟发货
 */
class OrderVirtualShipAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = null;
}
