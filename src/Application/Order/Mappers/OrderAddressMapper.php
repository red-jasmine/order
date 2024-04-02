<?php

namespace RedJasmine\Order\Application\Order\Mappers;

use RedJasmine\Order\Domain\Order\Models\OrderAddress;

class OrderAddressMapper
{

    public function formData(OrderAddress $orderAddress):OrderAddress
    {
        // TODO 设置
        return  OrderAddress::make($orderAddress->toArray());
    }

}
