<?php

namespace RedJasmine\Order\Services\Refund\Data;

use RedJasmine\Order\Services\Refund\Enums\RefundGoodsStatusEnum;
use RedJasmine\Order\Services\Refund\Enums\RefundTypeEnum;
use RedJasmine\Support\Data\Data;

class OrderProductRefundData extends Data
{


    /**
     * 退款类型
     * @var RefundTypeEnum
     */
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

    public function setFreightAmount(float|int|string|null $freightAmount) : OrderProductRefundData
    {
        $this->freightAmount = $freightAmount;
        return $this;
    }


}
