<?php

namespace RedJasmine\Order\Actions\Shipping;

use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderShippingDTO;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
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


    public function shipping(Order $order, OrderShippingDTO $orderShippingDTO) : Order
    {
        $order->products;
        // 未发货的 的订单商品 TODO 需要优化
        $order->products
            ->where('shipping_status', ShippingStatusEnum::WAIT_SEND)
            ->each(function (OrderProduct $orderProduct) use ($orderShippingDTO) {
                if ($orderShippingDTO->isSplit === false || in_array($orderProduct->id, $orderShippingDTO->orderProducts ?? [], true)) {
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
        // TODO 存在退款单 那么就直接关闭？

        return $order;
    }

}
