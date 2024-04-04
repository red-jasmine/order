<?php

namespace RedJasmine\Order\Domains\Order\Application\Mappers;


use RedJasmine\Order\Domains\Order\Application\Data\OrderAddressData;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderAddress;

class OrderAddressMapper
{

    public function fromData(OrderAddressData $orderAddressData, OrderAddress $orderAddress) : OrderAddress
    {
        // TODO 设置
        $orderAddress->fill($orderAddressData->toArray());
        return $orderAddress;
    }

}
