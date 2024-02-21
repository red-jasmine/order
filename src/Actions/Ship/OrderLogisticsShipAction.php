<?php

namespace RedJasmine\Order\Actions\Ship;

use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;

/**
 * 物流发货
 */
class OrderLogisticsShipAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = null;
}
