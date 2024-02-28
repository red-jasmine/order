<?php

namespace RedJasmine\Order\Actions\Others;

use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\DataTransferObjects\Others\OrderSellerCustomStatusDTO;
use RedJasmine\Order\Models\Order;

/**
 * 订单 卖家自定义状态
 */
class OrderSellerCustomStatusAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.sellerCustomStatus';

    public function isAllow(Order $order) : bool
    {
        return true;
    }

    /**
     * 隐藏
     *
     * @param int                        $id
     * @param OrderSellerCustomStatusDTO $DTO
     *
     * @return Order
     */
    public function execute(int $id, OrderSellerCustomStatusDTO $DTO) : Order
    {
        $order = $this->service->find($id);
        $order->setDTO($DTO);
        $this->isAllow($order);
        $this->pipelines($order);
        $this->pipeline->before();
        $this->pipeline->then(fn(Order $order) => $this->sellerCustomStatus($order, $DTO));
        $this->pipeline->after();
        return $order;
    }

    public function sellerCustomStatus(Order $order, OrderSellerCustomStatusDTO $DTO) : Order
    {
        $order->seller_custom_status = $DTO->sellerCustomStatus;
        $order->updater              = $this->service->getOperator();
        $order->save();
        return $order;
    }

}
