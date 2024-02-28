<?php

namespace RedJasmine\Order\Actions\Refunds;

use RedJasmine\Order\DataTransferObjects\Others\OrderSellerCustomStatusDTO;
use RedJasmine\Order\Models\OrderRefund;

/**
 * 订单 卖家自定义状态
 */
class RefundSellerCustomStatusAction extends AbstractRefundAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.sellerCustomStatus';

    public function isAllow(OrderRefund $orderRefund) : bool
    {
        return true;
    }

    /**
     * 隐藏
     *
     * @param int                        $id
     * @param OrderSellerCustomStatusDTO $DTO
     *
     * @return OrderRefund
     */
    public function execute(int $id, OrderSellerCustomStatusDTO $DTO) : OrderRefund
    {
        $orderRefund = $this->service->find($id);
        $orderRefund->setDTO($DTO);
        $this->isAllow($orderRefund);
        $this->pipelines($orderRefund);
        $this->pipeline->before();
        $this->pipeline->then(fn(OrderRefund $orderRefund) => $this->sellerCustomStatus($orderRefund, $DTO));
        $this->pipeline->after();
        return $orderRefund;
    }

    public function sellerCustomStatus(OrderRefund $orderRefund, OrderSellerCustomStatusDTO $DTO) : OrderRefund
    {
        $orderRefund->seller_custom_status = $DTO->sellerCustomStatus;
        $orderRefund->updater              = $this->service->getOperator();
        $orderRefund->save();
        return $orderRefund;
    }

}
