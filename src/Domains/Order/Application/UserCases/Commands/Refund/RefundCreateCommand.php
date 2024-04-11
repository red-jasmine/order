<?php

namespace RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Refund;

use RedJasmine\Order\Domains\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Support\Data\Data;

class RefundCreateCommand extends Data
{


    public int $id;

    public int $orderProductId;


    public RefundTypeEnum $refundType;


    public ?string $refundAmount = null;


    /**
     * 描述
     * @var string|null
     */
    public ?string $description;


    /**
     * 外部退款单ID
     * @var string|null
     */
    public ?string $outerRefundId = null;


    /**
     * 包含邮费
     * @var string|int|float|null
     */
    protected string|int|float|null $freightAmount = 0;

}
