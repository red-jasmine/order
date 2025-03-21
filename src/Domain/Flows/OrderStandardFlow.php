<?php

namespace RedJasmine\Order\Domain\Flows;

use RedJasmine\Order\Domain\Models\Enums\AcceptStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\RateStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\SettlementStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderProduct;

/**
 * 订单标准流程
 */
class OrderStandardFlow implements OrderFlowInterface
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

        // 设置 支付超时时间

    }

    public function paid(Order $order) : void
    {
        if ($order->payment_status !== PaymentStatusEnum::PAID) {
            return;
        }

        // 设置订单为 等待卖家确认中
        $order->order_status  = OrderStatusEnum::WAIT_SELLER_ACCEPT;
        $order->accept_status = AcceptStatusEnum::WAIT_ACCEPT;
        $order->products->each(function (OrderProduct $product) {
            $product->order_status = OrderStatusEnum::WAIT_SELLER_ACCEPT;
        });

        // 为自定接受
        if ($order->accept_wait_max_time === 0) {
            $order->accept();
        }else{
            // 设置最大自动接单时间
        }


    }

    /**
     * 接单操作
     * @param Order $order
     * @return void
     */
    public function accept(Order $order) : void
    {
        $order->order_status    = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
        $order->shipping_status = ShippingStatusEnum::WAIT_SEND;
        $order->products->each(function (OrderProduct $product) {
            $product->order_status    = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
            $product->shipping_status = ShippingStatusEnum::WAIT_SEND;
        });
    }

    public function reject(Order $order) : void
    {

    }


    public function shipping(Order $order) : void
    {
        $order->products->each(function (OrderProduct $product) {
            if ($product->shipping_status === ShippingStatusEnum::SHIPPED && $product->isEffective()) {
                $product->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
            }
        });

    }

    public function shipped(Order $order) : void
    {

        $order->products->each(function (OrderProduct $product) {
            if ($product->shipping_status === ShippingStatusEnum::SHIPPED && $product->isEffective()) {
                $product->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
            }
        });
        if ($order->shipping_status === ShippingStatusEnum::SHIPPED) {
            $order->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;


            // 设置最大确认时间 TODO
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
            if ($product->shipping_status !== ShippingStatusEnum::SHIPPED && $product->isEffective()) {
                $isAllConfirmed = false;
            }
        }
        if ($isAllConfirmed) {
            $order->order_status      = OrderStatusEnum::FINISHED;
            $order->rate_status       = RateStatusEnum::WAIT_RATE;
            $order->settlement_status = SettlementStatusEnum::WAIT_SETTLEMENT;
            $order->products->each(function (OrderProduct $product) {
                if ($product->isEffective()) {
                    $product->order_status      = OrderStatusEnum::FINISHED;
                    $product->rate_status       = RateStatusEnum::WAIT_RATE;
                    $product->settlement_status = SettlementStatusEnum::WAIT_SETTLEMENT;
                }
            });
        }


    }
}
