<?php

namespace RedJasmine\Order\Domain\Services;

use RedJasmine\Order\Domain\Enums\RefundPhaseEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Exceptions\OrderException;
use RedJasmine\Order\Domain\Exceptions\RefundException;
use RedJasmine\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Support\Domain\Models\ValueObjects\Amount;

class OrderRefundService
{

    /**
     * @param Order       $order
     * @param OrderRefund $orderRefund
     *
     * @return void
     * @throws RefundException
     */
    public function create(Order $order, OrderRefund $orderRefund) : void
    {
        /**
         * @var $orderProduct OrderProduct
         */
        $orderProduct = $order->products->where('id', $orderRefund->order_product_id)->firstOrFail();

        // TODO 验证是否允许 创建售后单

        // 获取允许的类型
        if (!in_array($orderRefund->refund_type, $orderProduct->allowRefundTypes(), true)) {
            throw RefundException::newFromCodes(RefundException::REFUND_TYPE_NOT_ALLOW);
        }

        // 填充 子商品单
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
        $orderRefund->shipping_status        = $orderProduct->shipping_status;
        $orderRefund->creator                = $order->getOperator();

        // 获取当售后阶段
        $orderRefund->phase = $this->getRefundPhase($orderProduct);
        // 计算退款金额 // TODO
        switch ($orderRefund->refund_type) {
            case RefundTypeEnum::REFUND:
                $orderRefund->refund_amount = new Amount($orderProduct->maxRefundAmount());
                $orderRefund->refund_status = RefundStatusEnum::WAIT_SELLER_AGREE;
                break;
            case RefundTypeEnum::RETURN_GOODS_REFUND:
                $orderRefund->refund_amount = new Amount($orderProduct->maxRefundAmount());
                $orderRefund->refund_status = RefundStatusEnum::WAIT_SELLER_AGREE_RETURN;
                break;
            case RefundTypeEnum::GUARANTEE:
                // TODO 需要
            case RefundTypeEnum::EXCHANGE:
            case RefundTypeEnum::SERVICE:
            case RefundTypeEnum::RESHIPMENT:
                $orderRefund->refund_amount = new Amount(0);
                $orderRefund->refund_status = RefundStatusEnum::WAIT_SELLER_AGREE_RETURN;
                break;
        }

        $orderRefund->created_time = now();
        // 设置订单商品状态
        $order->refunds->add($orderRefund);
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
