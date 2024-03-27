<?php

namespace RedJasmine\Order\DataTransferObjects\Refund;

use RedJasmine\Order\Enums\Logistics\LogisticsStatusEnum;
use RedJasmine\Support\Data\Data;

class RefundReturnGoodsDTO extends Data
{

    public string $expressCompanyCode;

    public string $expressNo;

    public LogisticsStatusEnum $status = LogisticsStatusEnum::CREATED;

}
