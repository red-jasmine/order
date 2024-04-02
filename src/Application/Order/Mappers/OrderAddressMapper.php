<?php

namespace RedJasmine\Order\Application\Order\Mappers;

use RedJasmine\Order\Application\Order\Data\OrderAddressData;
use RedJasmine\Order\Domain\Order\Models\OrderAddress;

class OrderAddressMapper
{

    public function formData(OrderAddressData $orderAddressData) : OrderAddress
    {
        // TODO 设置
        return OrderAddress::make($orderAddressData->toArray());
    }

}
