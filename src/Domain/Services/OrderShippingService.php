<?php

namespace RedJasmine\Order\Domain\Services;

use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderLogistics;
use RedJasmine\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domain\Models\OrderProductCardKey;

class OrderShippingService
{

    /**
     * 物流发货
     *
     * @param Order          $order
     * @param bool           $isSplit
     * @param OrderLogistics $logistics
     *
     * @return void
     * @throws OrderException
     */
    public function logistics(Order $order, bool $isSplit = false, OrderLogistics $logistics) : void
    {

        if (!in_array($order->shipping_type, [ ShippingTypeEnum::EXPRESS, ShippingTypeEnum::CITY_DELIVERY ], true)) {
            throw OrderException::newFromCodes(OrderException::SHIPPING_TYPE_NOT_ALLOW, '发货类型不支持操作');
        }
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
     * 卡密发货
     *
     * @param Order               $order
     * @param OrderProductCardKey $orderProductCardKey
     *
     * @return void
     * @throws OrderException
     */
    public function cardKey(Order $order, OrderProductCardKey $orderProductCardKey) : void
    {

        $orderProduct = $order->products->where('id', $orderProductCardKey->order_product_id)->firstOrFail();

        if ($orderProduct->shipping_type !== ShippingTypeEnum::CDK) {
            throw OrderException::newFromCodes(OrderException::SHIPPING_TYPE_NOT_ALLOW, '发货类型不支持操作');
        }

        if ($orderProduct->shipping_status === ShippingStatusEnum::SHIPPED) {
            throw OrderException::newFromCodes(OrderException::ORDER_STATUS_NOT_ALLOW);
        }

        $orderProductCardKey->creator = $order->getOperator();
        $orderProduct->addCardKey($orderProductCardKey);

        $orderProduct->shipping_status = ShippingStatusEnum::PART_SHIPPED;
        $orderProduct->shipping_time   = $orderProduct->shipping_time ?? now();
        $orderProduct->updater         = $order->getOperator();

        if ($orderProduct->progress >= $orderProduct->num) {
            $orderProduct->shipping_status = ShippingStatusEnum::SHIPPED;
            $orderProduct->collect_time    = now(); // 虚拟商品作为最后一次发货时间
        }

        $order->shipping();
    }


    /**
     * 虚拟发货
     *
     * @param Order $order
     * @param int   $orderProductId
     * @param bool  $isFinished 是否完成发货
     *
     * @return void
     * @throws OrderException
     */
    public function virtual(Order $order, int $orderProductId, bool $isFinished = true) : void
    {
        $orderProduct = $order->products->where('id', $orderProductId)->firstOrFail();

        if ($orderProduct->shipping_type !== ShippingTypeEnum::VIRTUAL) {
            throw OrderException::newFromCodes(OrderException::SHIPPING_TYPE_NOT_ALLOW, '发货类型不支持操作');
        }

        if ($orderProduct->shipping_status === ShippingStatusEnum::SHIPPED) {
            throw OrderException::newFromCodes(OrderException::ORDER_STATUS_NOT_ALLOW);
        }
        $orderProduct->shipping_status = $isFinished ? ShippingStatusEnum::SHIPPED : ShippingStatusEnum::PART_SHIPPED;
        $orderProduct->shipping_time   = $orderProduct->shipping_time ?? now();
        if ($orderProduct->shipping_status === ShippingStatusEnum::SHIPPED) {
            $orderProduct->collect_time = now(); // 虚拟商品作为最后一次发货时间
        }
        $order->shipping();
    }

}
