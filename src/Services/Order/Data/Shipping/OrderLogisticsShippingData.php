<?php

namespace RedJasmine\Order\Services\Order\Data\Shipping;

use RedJasmine\Order\Enums\Logistics\LogisticsStatusEnum;

class OrderLogisticsShippingData extends OrderShippingData
{

    public string $expressCompanyCode;

    public string $expressNo;

    /**
     * 物流状态
     * @var LogisticsStatusEnum
     */
    public LogisticsStatusEnum $status = LogisticsStatusEnum::CREATED;

}
