<?php

namespace RedJasmine\Order\Domain\Services;

use RedJasmine\Order\Domain\Enums\RefundPhaseEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;

class OrderRefundService
{


    public function create(Order $order, OrderRefund $orderRefund):void
    {
        $orderProduct = $order->products->where('id', $orderRefund->order_product_id)->firstOrFail();

        $orderRefund->seller                 = $order->seller;
        $orderRefund->buyer                  = $order->buyer;
        $orderRefund->shipping_type          = $orderProduct->shipping_type;
        $orderRefund->order_product_type     = $orderProduct->order_product_type;
        $orderRefund->product_type           = $orderProduct->product_type;
        $orderRefund->product_id             = $orderProduct->product_id;
        $orderRefund->sku_id                 = $orderProduct->sku_id;
        $orderRefund->title                  = $orderProduct->title;
        $orderRefund->sku_name               = $orderProduct->sku_name;
        $orderRefund->image                  = $orderProduct->image;
        $orderRefund->category_id            = $orderProduct->category_id;
        $orderRefund->seller_category_id     = $orderProduct->seller_category_id;
        $orderRefund->outer_id               = $orderProduct->outer_id;
        $orderRefund->outer_sku_id           = $orderProduct->outer_sku_id;
        $orderRefund->barcode                = $orderProduct->barcode;
        $orderRefund->price                  = $orderProduct->price;
        $orderRefund->cost_price             = $orderProduct->cost_price;
        $orderRefund->product_amount         = $orderProduct->product_amount;
        $orderRefund->payable_amount         = $orderProduct->payable_amount;
        $orderRefund->payment_amount         = $orderProduct->payment_amount;
        $orderRefund->divided_payment_amount = $orderProduct->divided_payment_amount;


        // 判断
        $orderRefund->phase         = $this->getRefundPhase($orderProduct);
        $orderRefund->refund_status = $orderRefund->refund_type === RefundTypeEnum::REFUND_ONLY ? RefundStatusEnum::WAIT_SELLER_AGREE : RefundStatusEnum::WAIT_SELLER_AGREE_RETURN;
        $orderRefund->created_time  = now();

        // 设置订单商品状态

        $order->refunds->push($orderRefund);
    }


    /**
     * 获取退款售后单阶段
     *
     * @param OrderProduct $orderProduct
     *
     * @return RefundPhaseEnum
     */
    protected function getRefundPhase(OrderProduct $orderProduct) : RefundPhaseEnum
    {
        if ($orderProduct->order_status === OrderStatusEnum::FINISHED) {
            RefundPhaseEnum::AFTER_SALE;
        }
        return RefundPhaseEnum::ON_SALE;
    }

}
