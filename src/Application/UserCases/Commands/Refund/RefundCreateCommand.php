<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Refund;

use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Support\Data\Data;

class RefundCreateCommand extends Data
{


    public int $id;

    public int $orderProductId;

    /**
     * 图片
     * @var array|null
     */
    public ?array $images;

    public RefundTypeEnum $refundType;


    public ?string $refundAmount = null;
    public ?string $reason;

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
