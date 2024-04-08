<?php

namespace RedJasmine\Order\Domains\Order\Domain;

use Illuminate\Database\Eloquent\Collection;
use RedJasmine\Order\Domains\Order\Domain\Models\Order;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderAddress;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderInfo;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderLogistics;
use RedJasmine\Order\Domains\Order\Domain\Models\OrderProductInfo;
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
        $order->setRelation('products', new Collection([]));
        $order->setRelation('payments', new Collection([]));
        $order->setRelation('address', null);
        return $order;
    }


    public function createOrderProduct() : OrderProduct
    {
        $orderProduct     = new OrderProduct();
        $orderProduct->id = $this->buildID();
        $info             = new OrderProductInfo();
        $info->id         = $orderProduct->id;
        $orderProduct->setRelation('info', $info);
        return $orderProduct;
    }


    public function createOrderAddress() : OrderAddress
    {
        $address = new OrderAddress();

        return $address;
    }


    public function createOrderPayment() : OrderPayment
    {
        $payment     = new OrderPayment();
        $payment->id = $this->buildID();
        return $payment;
    }

    public function createOrderLogistics() : OrderLogistics
    {
        $orderLogistics     = new OrderLogistics();
        $orderLogistics->id = $this->buildID();
        return $orderLogistics;
    }


}
