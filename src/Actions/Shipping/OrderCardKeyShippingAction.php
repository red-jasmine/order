<?php

namespace RedJasmine\Order\Actions\Shipping;


use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderShippedEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;

/**
 * 卡密发货
 */
class OrderCardKeyShippingAction extends AbstractOrderShippingAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.cardKeyShipping';


    /**
     * @param int        $id
     * @param bool       $isAllOrderProducts
     * @param array|null $orderProducts
     *
     * @return Order
     * @throws OrderException
     * @throws Throwable
     */
    public function execute(int $id, bool $isAllOrderProducts = true, ?array $orderProducts = null) : Order
    {
        // 如果是全部发货
        try {
            DB::beginTransaction();
            $order = $this->service->find($id);
            $this->isAllow($order);
            $pipelines = $this->pipelines($order);
            $pipelines->before();
            $pipelines->then(fn($order) => $this->shipping($order, $isAllOrderProducts, $orderProducts));
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
