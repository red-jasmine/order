<?php

namespace RedJasmine\Order\Actions\Others;

use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\Models\Order;

/**
 * 隐藏
 */
class OrderBuyerHiddenAction extends AbstractOrderAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.buyerHidden';

    /**
     * @param Order $order
     *
     * @return bool
     */
    public function isAllow(Order $order) : bool
    {
        return true;
    }

    /**
     * 隐藏
     *
     * @param int $id
     *
     * @return Order
     */
    public function execute(int $id) : Order
    {
        $order = $this->service->find($id);
        $this->isAllow($order);
        $this->pipelines($order);
        $this->pipeline->before();
        $this->pipeline->then(fn(Order $order) => $this->hidden($order));
        $this->pipeline->after();
        return $order;
    }

    public function hidden(Order $order) : Order
    {
        $order->is_buyer_delete = true;
        $order->updater         = $this->service->getOperator();
        $order->save();
        return $order;
    }

}
