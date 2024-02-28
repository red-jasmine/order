<?php

namespace RedJasmine\Order\DataTransferObjects\Refund;

use RedJasmine\Order\Enums\Refund\RefundGoodsStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Support\DataTransferObjects\Data;

class OrderProductRefundDTO extends Data
{


    public RefundTypeEnum $refundType;

    /**
     * 原因
     * @var string
     */
    public string $reason;

    /**
     * 退款金额
     * @var string|int|float|null
     */
    public string|int|float|null $refundAmount = null;
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
     * 图片
     * @var array|null
     */
    public ?array $images;

    /**
     * 货物状态
     * @var RefundGoodsStatusEnum|null
     */
    public ?RefundGoodsStatusEnum $goodStatus = null;


    /**
     * 包含邮费
     * @var string|int|float|null
     */
    protected string|int|float|null $freightAmount = 0;

    public function getFreightAmount() : float|int|string|null
    {
        return $this->freightAmount;
    }

    public function setFreightAmount(float|int|string|null $freightAmount) : OrderProductRefundDTO
    {
        $this->freightAmount = $freightAmount;
        return $this;
    }


}
