<?php

namespace RedJasmine\Order\Domains\Order\Domain\Services;

use RedJasmine\Order\Domains\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Models\Order;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderLogistics;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProductCardKey;

class OrderShippingService
{

    public function logistics(Order $order, bool $isSplit = false, OrderLogistics $logistics)
    {
        // 添加物流记录

        $order->addLogistics($logistics);
        // 设置 订单商品未发货状态
        $order->products
            ->whereIn('shipping_status', [ null, ShippingStatusEnum::WAIT_SEND ])
            ->each(function (OrderProduct $orderProduct) use ($isSplit, $logistics) {
                if ($isSplit === false || in_array($orderProduct->id, $logistics->order_product_id ?? [], true)) {
                    $orderProduct->shipping_status = ShippingStatusEnum::SHIPPED;
                    $orderProduct->shipping_time   = now();
                }
            });

        // 计算订单 发货状态
        $order->shipping();


    }


    public function cardKey(Order $order, OrderProductCardKey $orderProductCardKey)
    {

    }

    public function virtual()
    {

    }

}
