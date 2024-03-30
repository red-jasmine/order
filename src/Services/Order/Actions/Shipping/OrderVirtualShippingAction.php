<?php

namespace RedJasmine\Order\Services\Order\Actions\Shipping;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderVirtualShippingData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderVirtualShippingData $data
 */
class OrderVirtualShippingAction extends AbstractOrderShippingAction
{


    protected ?string $dataClass = OrderVirtualShippingData::class;


    /**
     * @param int                            $id
     * @param OrderVirtualShippingData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderVirtualShippingData|array $data = []) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }

    protected function handle() : Model
    {
        // 填充更多数据 TODO
        $order             = $this->model;
        $orderShippingData = $this->data;
        $order             = $this->shipping($order, $orderShippingData);
        $order->push();
        return $order;
    }

}
