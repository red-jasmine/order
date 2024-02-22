<?php

namespace RedJasmine\Order\Actions\Shipping;

use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;

abstract class AbstractOrderShippingAction extends AbstractOrderAction
{

    /**
     * 订单状态
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = [
        OrderStatusEnum::WAIT_SELLER_SEND_GOODS,
    ];

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = [
        PaymentStatusEnum::PAID,
        PaymentStatusEnum::NO_PAYMENT,
    ];


    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = [
        ShippingStatusEnum::WAIT_SEND,
        ShippingStatusEnum::PART_SHIPPED,
    ];


    /**
     * @param Order $order
     *
     * @return bool
     * @throws OrderException
     */
    public function isAllow(Order $order) : bool
    {
        $this->allowStatus($order);
        return true;
    }


    public function shipping(Order $order, bool $isAllOrderProducts = true, ?array $orderProducts = null) : Order
    {
        $order->products;
        // 未发货的 的订单商品 TODO
        $order->products
            ->where('shipping_status', ShippingStatusEnum::WAIT_SEND)
            ->each(function (OrderProduct $orderProduct) use ($isAllOrderProducts, $orderProducts) {
                if ($isAllOrderProducts === true || in_array($orderProduct->id, $orderProducts, true)) {
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
            $order->order_status = OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS;
        }

        return $order;
    }

}
