<?php

namespace RedJasmine\Order\Actions\Shipping;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderLogisticsShippingDTO;
use RedJasmine\Order\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderShippedEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderLogistics;
use RedJasmine\Support\Exceptions\AbstractException;

;

/**
 * 物流发货
 */
class OrderLogisticsShippingAction extends AbstractOrderShippingAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.logisticsShipping';

    /**
     * @param int                       $id
     * @param OrderLogisticsShippingDTO $orderShippingDTO
     *
     * @return Order
     * @throws OrderException
     */
    public function execute(int $id, OrderLogisticsShippingDTO $orderShippingDTO) : Order
    {
        // 如果是全部发货

        try {
            DB::beginTransaction();
            $order = $this->service->find($id);
            $order->setDTO($orderShippingDTO);
            $this->isAllow($order);
            $pipelines = $this->pipelines($order);
            $pipelines->before();
            // 添加物流单
            $pipelines->then(fn($order) => $this->logisticsShipping($order, $orderShippingDTO));
            $order->push();
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $pipelines->after();

        if ($order->shipping_status === ShippingStatusEnum::SHIPPED) {
            OrderShippedEvent::dispatch($order);
        }
        return $order;

    }

    public function logisticsShipping(Order $order, OrderLogisticsShippingDTO $orderShippingDTO) : Order
    {
        $orderLogistics                       = new OrderLogistics();
        $orderLogistics->seller               = $order->seller;
        $orderLogistics->buyer                = $order->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
        $orderLogistics->order_product_id     = $orderShippingDTO->orderProducts;
        $orderLogistics->express_company_code = $orderShippingDTO->expressCompanyCode;
        $orderLogistics->express_no           = $orderShippingDTO->expressNo;
        $orderLogistics->status               = $orderShippingDTO->status;
        $orderLogistics->shipping_time        = now();
        $orderLogistics->creator              = $this->service->getOperator();
        $order->logistics()->save($orderLogistics);
        return $this->shipping($order, $orderShippingDTO);

    }

}
