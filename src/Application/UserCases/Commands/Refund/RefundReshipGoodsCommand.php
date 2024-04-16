<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Refund;

use RedJasmine\Order\Domain\Enums\Logistics\LogisticsStatusEnum;
use RedJasmine\Support\Data\Data;

class RefundReshipGoodsCommand extends Data
{

    public int $rid; // 退款单ID

    /**
     * 快递公司
     *  TODO 改为值对象
     * @var string
     */
    public string $expressCompanyCode;

    /**
     * 快递单号
     * @var string
     */
    public string $expressNo;


    public LogisticsStatusEnum $status = LogisticsStatusEnum::CREATED;


}
