<?php

namespace RedJasmine\Order\Actions\Shipping;

use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;

/**
 * 物流发货
 */
class OrderLogisticsShippingAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = null;
}
