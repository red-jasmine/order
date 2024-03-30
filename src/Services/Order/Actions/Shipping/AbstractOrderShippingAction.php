<?php

namespace RedJasmine\Order\Services\Order\Actions\Shipping;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderShippingData;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;

abstract class AbstractOrderShippingAction extends AbstractOrderAction
{

    protected bool $lockForUpdate = true;

    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = [
        null,
        ShippingStatusEnum::WAIT_SEND,
        ShippingStatusEnum::PART_SHIPPED,
    ];

    protected function fill(array $data) : ?Model
    {
        return $this->model;
    }


    public function shipping(Order $order, OrderShippingData $orderShippingData) : Order
    {
        $order->products;
        // 未发货的 的订单商品 TODO 需要优化
        $order->products
            ->where('shipping_status', ShippingStatusEnum::WAIT_SEND)
            ->each(function (OrderProduct $orderProduct) use ($orderShippingData) {
                if ($orderShippingData->isSplit === false || in_array($orderProduct->id, $orderShippingData->orderProducts ?? [], true)) {
                    $orderProduct->shipping_status = ShippingStatusEnum::SHIPPED;
                    $orderProduct->order_status    = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
                    $orderProduct->shipping_time   = now();
                }
            });

        // 查询未发货的订单商品
        // TODO  正常有效单订单商品 未退款
        $count = $order->products->whereIn('shipping_status', [ null, ShippingStatusEnum::WAIT_SEND ])->count();
        // 如果还有未发货的订单商品 那么订单只能是部分发货
        $order->shipping_status = $count > 0 ? ShippingStatusEnum::PART_SHIPPED : ShippingStatusEnum::SHIPPED;
        $order->shipping_time   = $order->shipping_time ?? now();


        // 如果都发货了，那么久状态流转
        if ($order->shipping_status === ShippingStatusEnum::SHIPPED) {
            if ($order->order_status === OrderStatusEnum::WAIT_SELLER_SEND_GOODS) {
                $order->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
            }
        }

        return $order;
    }

}
