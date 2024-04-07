<?php

namespace RedJasmine\Order\Domains\Order\Domain\Services;

use RedJasmine\Order\Domains\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Models\Order;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderLogistics;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProduct;

class OrderShippingService
{


    public function logistics(Order $order, OrderLogistics $logistics)
    {
        // 添加物流记录
        $order->addLogistics($logistics);
        // 设置 订单商品未发货状态
        $order->products
            ->where('shipping_status', ShippingStatusEnum::WAIT_SEND)
            ->each(function (OrderProduct $orderProduct) use ($logistics) {
                $orderProduct->shipping_status = ShippingStatusEnum::SHIPPED;
                $orderProduct->shipping_time   = now();
            });
        // 计算订单 发货状态

        // TODO


    }


    public function cardKey()
    {

    }

    public function virtual()
    {

    }

}
