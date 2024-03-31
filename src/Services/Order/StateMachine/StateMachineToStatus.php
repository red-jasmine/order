<?php

namespace RedJasmine\Order\Services\Order\StateMachine;

use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RateStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RefundStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;

class StateMachineToStatus
{


    public array $from = [];


    public array $to = [];


    public ?OrderStatusEnum $orderStatus;

    public ?PaymentStatusEnum $paymentStatus;

    public ?ShippingStatusEnum $shippingStatus;

    public ?RefundStatusEnum $refundStatus;

    public ?RateStatusEnum $rateStatus;


    /**
     * @var array<OrderStatusEnum>|null
     */
    public array|null $allowOrderStatus = null;

    /**
     * @var array<OrderStatusEnum>|null
     */
    public array|null $disallowOrderStatus = null;

}
