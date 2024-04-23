<?php

namespace RedJasmine\Order\Domain\Observer;

use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Strategies\OrderFlowInterface;
use RedJasmine\Order\Domain\Strategies\OrderSopFlow;

/**
 * 标准电商流程
 */
class OrderObserver
{
    // 根据不同的 订单类型 走不同的策略


    protected function orderFlow(Order $order) : OrderFlowInterface
    {
        switch ($order->order_type) {
            case OrderTypeEnum::SOP:
                return app(OrderSopFlow::class);
                break;
            case OrderTypeEnum::PRESALE:
                return app(OrderSopFlow::class);
            default:
                return app(OrderSopFlow::class);
                break;
        }

    }

    public function creating(Order $order) : void
    {

        $this->orderFlow($order)->creating($order);

    }

    public function paid(Order $order) : void
    {
        $this->orderFlow($order)->paid($order);

    }

    public function shipped(Order $order) : void
    {
        $this->orderFlow($order)->shipped($order);
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
        $this->orderFlow($order)->confirmed($order);
    }

}
