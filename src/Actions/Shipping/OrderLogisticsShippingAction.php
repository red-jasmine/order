<?php

namespace RedJasmine\Order\Actions\Shipping;

;

/**
 * 物流发货
 */
class OrderLogisticsShippingAction extends AbstractOrderShippingAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.logisticsShipping';

}
