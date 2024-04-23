<?php

namespace RedJasmine\Order\Domain\Strategies;

use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\RateStatusEnum;
use RedJasmine\Order\Domain\Enums\SettlementStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderProduct;

/**
 * 订单标准流程
 */
class OrderSopFlow implements OrderFlowInterface
{
    public function creating(Order $order) : void
    {
        // 初始化
        $order->order_status   = OrderStatusEnum::WAIT_BUYER_PAY;
        $order->payment_status = PaymentStatusEnum::WAIT_PAY;
        $order->products->each(function (OrderProduct $product) {
            $product->order_status   = OrderStatusEnum::WAIT_BUYER_PAY;
            $product->payment_status = PaymentStatusEnum::WAIT_PAY;
        });

    }

    public function paid(Order $order) : void
    {
        if ($order->payment_status !== PaymentStatusEnum::PAID) {
            return;
        }
        $order->order_status    = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
        $order->shipping_status = ShippingStatusEnum::WAIT_SEND;
        $order->products->each(function (OrderProduct $product) {
            $product->order_status    = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
            $product->shipping_status = ShippingStatusEnum::WAIT_SEND;
        });

    }

    public function shipped(Order $order) : void
    {
        $order->products->each(function (OrderProduct $product) {
            if ($product->shipping_status === ShippingStatusEnum::SHIPPED && $product->isAvailable()) {
                $product->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
            }
        });
        $isAllShipped = true;
        foreach ($order->products as $product) {
            if ($product->isAvailable() && $product->shipping_status === ShippingStatusEnum::SHIPPED) {
                $isAllShipped = false;
            }
        }

        if ($isAllShipped) {
            $order->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
        }

    }

    /**
     * 订单确认
     *
     * @param Order $order
     *
     * @return void
     */
    public function confirmed(Order $order) : void
    {
        $isAllConfirmed = true;
        foreach ($order->products as $product) {
            if ($product->shipping_status !== ShippingStatusEnum::SHIPPED && $product->isAvailable()) {
                $isAllConfirmed = false;
            }
        }
        if ($isAllConfirmed) {
            $order->order_status      = OrderStatusEnum::FINISHED;
            $order->rate_status       = RateStatusEnum::WAIT_RATE;
            $order->settlement_status = SettlementStatusEnum::WAIT_SETTLEMENT;
            $order->products->each(function (OrderProduct $product) {
                if ($product->isAvailable()) {
                    $product->order_status      = OrderStatusEnum::FINISHED;
                    $product->rate_status       = RateStatusEnum::WAIT_RATE;
                    $product->settlement_status = SettlementStatusEnum::WAIT_SETTLEMENT;
                }
            });
        }


    }
}
