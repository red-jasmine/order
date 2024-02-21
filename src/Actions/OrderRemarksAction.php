<?php

namespace RedJasmine\Order\Actions;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Events\Orders\OrderCancelledEvent;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Exceptions\AbstractException;

class OrderRemarksAction extends AbstractOrderAction
{

    public function isAllow(Order $order) : bool
    {
        return true;

    }

    public function execute(int $id, string $remarks = null, ?int $orderProductId = null)
    {
        try {
            DB::beginTransaction();
            $order = $this->service->find($id);
            $this->isAllow($order);

            $orderProduct = $orderProductId ? $this->service->findOrderProduct($orderProductId) : null;

            $pipelines = $this->pipelines($order);
            $pipelines->before();
            $order = $pipelines->then(function (Order $order) use ($remarks, $orderProduct) {
                $this->setRemarks($order, $remarks, $orderProduct);
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
        $pipelines->after();
        OrderCancelledEvent::dispatch($order);

        return $order;
    }


    public function setRemarks(Order $order, string $remarks = null, ?OrderProduct $orderProduct = null)
    {
        $model = $orderProduct ?? $order;

    }


}
