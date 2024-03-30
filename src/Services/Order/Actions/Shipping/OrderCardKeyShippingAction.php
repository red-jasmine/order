<?php

namespace RedJasmine\Order\Services\Order\Actions\Shipping;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderCardKeyShippingData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderCardKeyShippingData $data
 */
class OrderCardKeyShippingAction extends AbstractOrderShippingAction
{


    protected ?string $dataClass = OrderCardKeyShippingData::class;


    /**
     * @param int                            $id
     * @param OrderCardKeyShippingData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderCardKeyShippingData|array $data = []) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }

    protected function handle() : Model
    {
        $order             = $this->model;
        $orderShippingData = $this->data;
        $order->products->each(function (OrderProduct $orderProduct) use ($orderShippingData) {
            if ($orderShippingData->isSplit === false || in_array($orderProduct->id, $orderShippingData->orderProducts ?? [], true)) {
                $orderProduct->info->card_key = $orderShippingData->cardKey;
            }
        });
        $order = $this->shipping($order, $orderShippingData);
        $order->push();
        return $order;
    }

}
