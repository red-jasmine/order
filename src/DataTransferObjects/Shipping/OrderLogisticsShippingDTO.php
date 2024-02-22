<?php

namespace RedJasmine\Order\DataTransferObjects\Shipping;

use RedJasmine\Order\Enums\Logistics\LogisticsStatusEnum;

class OrderLogisticsShippingDTO extends OrderShippingDTO
{

    public string $expressCompanyCode;

    public string $expressNo;

    public LogisticsStatusEnum $status = LogisticsStatusEnum::CREATED;

}
