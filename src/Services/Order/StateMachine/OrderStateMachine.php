<?php

namespace RedJasmine\Order\Services\Order\StateMachine;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RateStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;

class OrderStateMachine
{


    /**
     * 初始状态
     * @return string[]
     */
    public function defaultStatus() : array
    {
        return [
            'order_status'         => OrderStatusEnum::WAIT_BUYER_PAY,
            'payment_status'       => PaymentStatusEnum::WAIT_PAY,
            'shipping_status'      => null,
            'refund_status'        => null,
            'rate_status'          => null,
            'seller_custom_status' => null,
        ];
    }

    public function transitions() : array
    {
        return [
            'paying'   => [
                'from' => [
                    'order_status'   => [ OrderStatusEnum::WAIT_BUYER_PAY ],
                    'payment_status' => [ PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::PAYING ],
                ],
                'to'   => [
                    'payment_status' => PaymentStatusEnum::PAYING,
                ],
            ],
            'paid'     => [
                'from' => [
                    'order_status'    => [ OrderStatusEnum::WAIT_BUYER_PAY ],
                    'payment_status'  => [ PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::PAYING ],
                    'shipping_status' => [ null ],
                ],
                'to'   => [
                    'order_status'    => OrderStatusEnum::WAIT_SELLER_SEND_GOODS,
                    'payment_status'  => PaymentStatusEnum::PAID,
                    'shipping_status' => ShippingStatusEnum::WAIT_SEND,
                ],
            ],
            'shipping' => [
                'from' => [
                    'order_status'    => [ OrderStatusEnum::WAIT_SELLER_SEND_GOODS ],
                    'payment_status'  => [ PaymentStatusEnum::PAID ],
                    'shipping_status' => [ ShippingStatusEnum::WAIT_SEND ],
                ],
                'to'   => [
                    // 不分发货如何处理
                    'order_status'    => OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS,
                    'shipping_status' => ShippingStatusEnum::SHIPPED,
                ],
            ],
            'confirm'  => [
                'from' => [
                    'order_status'    => [ OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS ],
                    'shipping_status' => [ ShippingStatusEnum::SHIPPED ],
                ],
                'to'   => [
                    // 部分确认如何处理
                    'order_status' => OrderStatusEnum::FINISHED,
                    'rate_status'  => RateStatusEnum::WAIT_RATE,
                ],
            ],
            'cancel'   => [
                'from' => [
                    'order_status'   => [ OrderStatusEnum::WAIT_BUYER_PAY ],
                    'payment_status' => [ PaymentStatusEnum::WAIT_PAY, PaymentStatusEnum::PAYING ],
                ],
                'to'   => [
                    'order_status' => OrderStatusEnum::CANCEL,
                ],
            ],
        ];
    }
}
