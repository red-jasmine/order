<?php

namespace RedJasmine\Order\Domain\Services;

use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderLogistics;
use RedJasmine\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domain\Models\OrderProductCardKey;

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


    /**
     * @param Order               $order
     * @param OrderProductCardKey $orderProductCardKey
     *
     * @return void
     * @throws OrderException
     */
    public function cardKey(Order $order, OrderProductCardKey $orderProductCardKey) : void
    {
        $orderProductCardKey->seller   = $order->seller;
        $orderProductCardKey->buyer    = $order->buyer;
        $orderProductCardKey->order_id = $order->id;

        $orderProduct = $order->products->where('id', $orderProductCardKey->order_product_id)->firstOrFail();
        if ($orderProduct->shipping_status === ShippingStatusEnum::SHIPPED) {
            throw new OrderException('已完成发货');
        }

        $orderProduct->addCardKey($orderProductCardKey);
        $orderProduct->shipping_status = ShippingStatusEnum::PART_SHIPPED;
        $orderProduct->shipping_time   = $orderProduct->shipping_time ?? now();

        if ($orderProduct->cardKeys->count() >= $orderProduct->num) {
            $orderProduct->shipping_status = ShippingStatusEnum::SHIPPED;
        }
        ++$orderProduct->progress;

        $order->shipping();
    }

    /**
     * @throws OrderException
     */
    public function virtual(Order $order, int $orderProductId, bool $isPartShipped = false) : void
    {


        $orderProduct = $order->products->where('id', $orderProductId)->firstOrFail();

        if ($orderProduct->shipping_status === ShippingStatusEnum::SHIPPED) {
            throw new OrderException('已完成发货');
        }

        $orderProduct->shipping_status = $isPartShipped ? ShippingStatusEnum::PART_SHIPPED : ShippingStatusEnum::SHIPPED;
        $orderProduct->shipping_time   = now();
        $order->shipping();
    }

}
