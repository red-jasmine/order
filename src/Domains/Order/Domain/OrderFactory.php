<?php

namespace RedJasmine\Order\Domains\Order\Domain;

use RedJasmine\Order\Domains\Order\Domain\Models\Order;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderInfo;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProduct;
use RedJasmine\Support\Helpers\ID\Snowflake;

class OrderFactory
{
    // TODO  这里可用 抽象工厂
    /**
     * @return int
     * @throws \Exception
     */
    public function buildID() : int
    {
        return Snowflake::getInstance()->nextId();
    }


    public function createOrder() : Order
    {
        $order         = new Order();
        $order->id     = $this->buildID();
        $orderInfo     = new OrderInfo();
        $orderInfo->id = $order->id;
        $order->setRelation('info', $orderInfo);
        $order->setRelation('products', collect([]));
        $order->setRelation('address', null);
        return $order;
    }


    public function createOrderProduct() : OrderProduct
    {
        $orderProduct     = new OrderProduct();
        $orderProduct->id = $this->buildID();
        $info             = new OrderProduct();
        $info->id         = $orderProduct->id;
        $orderProduct->setRelation('info', $info);
        return $orderProduct;
    }


}
