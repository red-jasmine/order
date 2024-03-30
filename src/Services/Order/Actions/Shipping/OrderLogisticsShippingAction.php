<?php

namespace RedJasmine\Order\Services\Order\Actions\Shipping;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderLogistics;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderLogisticsShippingData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderLogisticsShippingData $data
 */
class OrderLogisticsShippingAction extends AbstractOrderShippingAction
{


    protected ?string $dataClass = OrderLogisticsShippingData::class;


    /**
     * @param int                              $id
     * @param OrderLogisticsShippingData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderLogisticsShippingData|array $data = []) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }

    protected function handle() : Model
    {
        $order                                = $this->model;
        $orderShippingData                    = $this->data;
        $orderLogistics                       = new OrderLogistics();
        $orderLogistics->seller               = $order->seller;
        $orderLogistics->buyer                = $order->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
        $orderLogistics->order_product_id     = $orderShippingData->orderProducts;
        $orderLogistics->express_company_code = $orderShippingData->expressCompanyCode;
        $orderLogistics->express_no           = $orderShippingData->expressNo;
        $orderLogistics->status               = $orderShippingData->status;
        $orderLogistics->shipping_time        = now();
        $orderLogistics->creator              = $this->service->getOperator();
        $order->logistics()->save($orderLogistics);
        $order = $this->shipping($order, $orderShippingData);
        $order->push();
        return $order;
    }

}
