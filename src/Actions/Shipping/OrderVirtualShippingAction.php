<?php

namespace RedJasmine\Order\Actions\Shipping;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderShippingDTO;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderShippedEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use Throwable;

/**
 * 虚拟发货
 */
class OrderVirtualShippingAction extends AbstractOrderShippingAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.virtualShipping';


    /**
     * @param int              $id
     * @param OrderShippingDTO $orderShippingDTO
     *
     * @return Order
     * @throws OrderException
     * @throws Throwable
     */
    public function execute(int $id, OrderShippingDTO $orderShippingDTO) : Order
    {
        // 如果是全部发货
        try {
            DB::beginTransaction();
            $order = $this->service->find($id);
            $this->isAllow($order);
            $order->setDTO($orderShippingDTO);
            $pipelines = $this->pipelines($order);
            $pipelines->before();
            $pipelines->then(fn($order) => $this->shipping($order, $orderShippingDTO));
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


}
