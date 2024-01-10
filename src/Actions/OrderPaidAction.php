<?php

namespace RedJasmine\Order\Actions;


use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Events\Orders\OrderPaidEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class OrderPaidAction extends AbstractOrderAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.paid';

    protected ?array $allowOrderStatus = [
        OrderStatusEnum::WAIT_BUYER_PAY,
    ];

    protected ?array $allowPaymentStatus = [
        PaymentStatusEnum::WAIT_PAY,
        PaymentStatusEnum::PAYING,
    ];

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
     * 支付成功
     *
     * @param int $id
     *
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function execute(int $id) : Order
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $order = $this->pipelines($order, function (Order $order) {
                $this->setPaid($order);
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

        OrderPaidEvent::dispatch($order);

        return $order;
    }


    /**
     * 设置为已支付
     *
     * @param Order $order
     *
     * @return Order
     */
    protected function setPaid(Order $order) : Order
    {
        $order->payment_status = PaymentStatusEnum::PAID;
        $order->payment_time   = now();
        $order->products->each(function (OrderProduct $product) {
            $product->payment_status = PaymentStatusEnum::PAID;
            $product->payment_time   = now();
        });
        return $order;
    }


}
