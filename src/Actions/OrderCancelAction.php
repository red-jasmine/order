<?php

namespace RedJasmine\Order\Actions;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderCancelledEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * 取消订单
 */
class OrderCancelAction extends AbstractOrderAction
{


    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.cancel';


    /**
     * 订单状态
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = [
        OrderStatusEnum::WAIT_BUYER_PAY,
    ];

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = [
        PaymentStatusEnum::WAIT_PAY,
        PaymentStatusEnum::NO_PAYMENT,
    ];


    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = null;


    /**
     * @param Order $order
     *
     * @return bool
     * @throws OrderException
     */
    public function isAllow(Order $order) : bool
    {
        $this->allowStatus($order);
        return true;
    }


    /**
     * @param int $id
     *
     * @return mixed
     * @throws AbstractException
     */
    public function execute(int $id) : Order
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $pipelines = $this->pipelines($order);
            $pipelines->before();
            $order = $pipelines->then(function (Order $order) {
                $this->cancel($order);
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

    protected function cancel(Order $order) : void
    {
        $order->order_status = OrderStatusEnum::CANCEL;
        $order->close_time   = now();
        $order->products->each(function (OrderProduct $product) use ($order) {
            $product->order_status = OrderStatusEnum::CANCEL;
            $product->close_time   = $order->close_time;
        });
    }

}
