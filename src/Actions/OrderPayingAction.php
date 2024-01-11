<?php

namespace RedJasmine\Order\Actions;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Events\Orders\OrderPayingEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use Throwable;

/**
 * 发起支付
 */
class OrderPayingAction extends AbstractOrderAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.paying';


    protected ?array $allowOrderStatus   = [
        OrderStatusEnum::WAIT_BUYER_PAY,
    ];
    protected ?array $allowPaymentStatus = [
        PaymentStatusEnum::NO_PAYMENT,
        PaymentStatusEnum::WAIT_PAY,
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
     * @param int $id
     *
     * @return bool|null
     * @throws OrderException
     * @throws Throwable
     */
    public function execute(int $id) : ?bool
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $this->pipelines($order)->then(function (Order $order) {
                $this->setPaying($order);
                $order->save();
                return $order;
            });
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);
            throw  $throwable;
        }

        // 触发事件
        OrderPayingEvent::dispatch($order);
        return true;

    }


    protected function setPaying(Order $order) : Order
    {
        $order->payment_status = PaymentStatusEnum::PAYING;
        $order->updater        = $this->service->getOperator();
        return $order;
    }


}
