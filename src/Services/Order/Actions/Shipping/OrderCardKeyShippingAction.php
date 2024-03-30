<?php

namespace RedJasmine\Order\Services\Order\Actions\Shipping;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderProductCardKey;
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

    /**
     * @return Model
     * @throws Exception
     */
    protected function handle() : Model
    {
        $order             = $this->model;
        $orderShippingData = $this->data;
        $order->products->each(function (OrderProduct $orderProduct) use ($orderShippingData) {
            if ($orderShippingData->isSplit === false || in_array($orderProduct->id, $orderShippingData->orderProducts ?? [], true)) {

                $cardKeys = [];
                foreach ($orderShippingData->contents as $contentData) {
                    $cardKey                   = new OrderProductCardKey();
                    $cardKey->id               = $this->service::buildID();
                    $cardKey->content          = $contentData->content;
                    $cardKey->status           = $contentData->status;
                    $cardKey->extends          = $contentData->extends;
                    $cardKey->seller           = $orderProduct->seller;
                    $cardKey->buyer            = $orderProduct->buyer;
                    $cardKey->order_product_id = $orderProduct->id;
                    $cardKey->order_id         = $orderProduct->order_id;
                    $cardKey->creator          = $this->service->getOperator();
                    $cardKeys[]                = $cardKey;
                }
                if (count($cardKeys)) {
                    $orderProduct->cardKeys()->saveMany($cardKeys);
                }
            }
        });
        // 这里不能直接采用 单个发货原理 TODO
        $order = $this->shipping($order, $orderShippingData);
        $order->push();
        return $order;
    }

}
