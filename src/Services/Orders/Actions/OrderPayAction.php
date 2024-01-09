<?php

namespace RedJasmine\Order\Services\Orders\Actions;

use DB;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use Throwable;

/**
 * 发起支付
 */
class OrderPayAction extends AbstractOrderAction
{

    protected array $allowOrderStatus   = [
        OrderStatusEnum::WAIT_BUYER_PAY,
    ];
    protected array $allowPaymentStatus = [
        PaymentStatusEnum::NO_PAYMENT,
        PaymentStatusEnum::WAIT_PAY,
    ];

    /**
     * @param Order $order
     *
     * @return void
     * @throws OrderException
     */
    public function stateMachine(Order $order) : void
    {
        if (!in_array($order->order_status, $this->allowOrderStatus, true)) {
            throw new OrderException();
        }
        if (!in_array($order->payment_status, $this->allowPaymentStatus, true)) {
            throw new OrderException($order->payment_status->label() . '不可操作',);
        }
    }

    /**
     * @param Order $order
     *
     * @return void
     * @throws OrderException
     */
    public function isAllow(Order $order) : void
    {
        $this->stateMachine($order);

    }

    /**
     * @param int $id
     *
     * @return void
     * @throws Throwable
     */
    public function pay(int $id)
    {
        try {
            DB::beginTransaction();
            $order = $this->service->find($id);
            $this->isAllow($order);
            $this->paying($order);
            $order->save();
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);
            throw  $throwable;
        }


    }


    protected function paying(Order $order) : Order
    {
        $order->payment_status = PaymentStatusEnum::PAYING;

        return $order;
    }


}
