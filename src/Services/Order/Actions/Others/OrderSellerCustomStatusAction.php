<?php

namespace RedJasmine\Order\Services\Order\Actions\Others;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\Services\Order\Data\OrderSellerCustomStatusData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderSellerCustomStatusData $data
 */
class OrderSellerCustomStatusAction extends AbstractOrderAction
{


    protected ?string $dataClass = OrderSellerCustomStatusData::class;


    /**
     * @param int                               $id
     * @param OrderSellerCustomStatusData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderSellerCustomStatusData|array $data) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }


    public function handle() : Model
    {
        $order                       = $this->model;
        $order->seller_custom_status = $this->data->sellerCustomStatus;
        $order->push();
        return $order;
    }
}
