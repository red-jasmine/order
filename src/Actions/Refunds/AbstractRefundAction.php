<?php

namespace RedJasmine\Order\Actions\Refunds;

use RedJasmine\Order\Services\Order\Enums\RefundStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Order\Services\RefundService;
use RedJasmine\Support\Foundation\Service\Action;

abstract class AbstractRefundAction extends Action
{

    protected ?RefundService $service;


    /**
     * @var array|null|RefundStatusEnum[]
     */
    protected ?array $allowRefundStatus = null;


    /**
     * @var array|null|RefundTypeEnum[]
     */
    protected ?array $allowRefundType = null;


    /**
     * @param OrderRefund $orderRefund
     *
     * @return bool
     * @throws RefundException
     */
    protected function allowStatus(OrderRefund $orderRefund) : bool
    {
        $this->checkEnums($orderRefund->refund_status, $this->allowRefundStatus);
        $this->checkEnums($orderRefund->refund_type, $this->allowRefundType);
        return true;
    }


    /**
     * @param            $status
     * @param array|null $allowStatus
     *
     * @return bool
     * @throws RefundException
     */
    protected function checkEnums($status, ?array $allowStatus) : bool
    {
        if ($allowStatus === null) {
            return true;
        }
        if (!in_array($status, $allowStatus, true)) {
            throw new RefundException($status->label() . ' 不支持操作');
        }
        return true;
    }

}
