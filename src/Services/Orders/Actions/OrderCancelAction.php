<?php

namespace RedJasmine\Order\Services\Orders\Actions;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Events\Orders\OrderCancelledEvent;
use RedJasmine\Order\Models\Order;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * 取消订单
 */
class OrderCancelAction extends AbstractOrderAction
{


    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.cancel';

    public function isAllow(Order $order) : bool
    {

        return true;
    }


    /**
     * @param int $id
     *
     * @return mixed
     * @throws AbstractException
     */
    public function cancel(int $id) : Order
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $order = $this->pipelines($order, function (Order $order) {
                $this->setCancel($order);
                $order->push();
                return $order;
            });
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }

        OrderCancelledEvent::dispatch($order);

        return $order;

    }

    protected function setCancel(Order $order) : void
    {

        $order->order_status = OrderStatusEnum::TRADE_CANCEL;
        $order->close_time   = now();

    }

}
