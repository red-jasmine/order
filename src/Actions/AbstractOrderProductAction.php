<?php

namespace RedJasmine\Order\Actions;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Foundation\Service\Action;

class AbstractOrderProductAction extends Action
{

    /**
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = null;

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = null;


    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = null;


    /**
     * @var array|null|RefundStatusEnum[]
     */
    protected ?array $allowRefundStatus = null;

    /**
     * @param OrderProduct $orderProduct
     *
     * @return bool
     * @throws OrderException
     */
    protected function allowStatus(OrderProduct $orderProduct) : bool
    {
        $this->checkStatus($orderProduct->order_status, $this->allowOrderStatus);
        $this->checkStatus($orderProduct->payment_status, $this->allowPaymentStatus);
        $this->checkStatus($orderProduct->shipping_status, $this->allowShippingStatus);
        $this->checkStatus($orderProduct->refund_status, $this->allowRefundStatus);
        return true;
    }


    /**
     * @param            $status
     * @param array|null $allowStatus
     *
     * @return bool
     * @throws OrderException
     */
    protected function checkStatus($status, ?array $allowStatus) : bool
    {
        if ($allowStatus === null) {
            return true;
        }
        if (!in_array($status, $allowStatus, true)) {
            throw new OrderException($status->label() . ' 不支持操作');
        }
        return true;
    }


}
