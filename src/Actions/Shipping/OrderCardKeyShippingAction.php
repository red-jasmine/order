<?php

namespace RedJasmine\Order\Actions\Shipping;


use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderCardKeyShippingDTO;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderShippedEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;

/**
 * 卡密发货
 */
class OrderCardKeyShippingAction extends AbstractOrderShippingAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.cardKeyShipping';

    /**
     * @param int                     $id
     * @param OrderCardKeyShippingDTO $orderShippingDTO
     *
     * @return Order
     * @throws OrderException
     */
    public function execute(int $id, OrderCardKeyShippingDTO $orderShippingDTO) : Order
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
            $pipelines->then(fn($order) => $this->cardKeyShipping($order, $orderShippingDTO));
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

    public function cardKeyShipping(Order $order, OrderCardKeyShippingDTO $orderShippingDTO) : Order
    {

        $order->products
            ->each(function (OrderProduct $orderProduct) use ($orderShippingDTO) {
                if ($orderShippingDTO->isSplit === false || in_array($orderProduct->id, $orderShippingDTO->orderProducts ?? [], true)) {
                    $orderProduct->info->card_key = $orderShippingDTO->cardKey;

                }
            });
        return $this->shipping($order, $orderShippingDTO);

    }
}
