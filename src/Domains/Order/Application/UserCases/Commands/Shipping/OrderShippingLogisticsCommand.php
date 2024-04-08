<?php

namespace RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping;

use RedJasmine\Order\Domains\Order\Domain\Enums\Logistics\LogisticsStatusEnum;
use RedJasmine\Support\Data\Data;

class OrderShippingLogisticsCommand extends Data
{

    public int $id;

    /**
     * 是否拆分
     * @var bool
     */
    public bool $isSplit = false;

    /**
     * 部分订单商品 集合
     * @var array|null
     */
    public ?array $orderProducts = null;


    /**
     * 快递公司
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
