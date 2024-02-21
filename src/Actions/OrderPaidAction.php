<?php

namespace RedJasmine\Order\Actions;


use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\OrderPaidInfoDTO;
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
     * @param int                   $id
     * @param OrderPaidInfoDTO|null $orderPaidInfoDTO
     *
     * @return Order
     * @throws AbstractException
     * @throws OrderException
     * @throws Throwable
     */
    public function execute(int $id, ?OrderPaidInfoDTO $orderPaidInfoDTO = null) : Order
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $order->setDTO($orderPaidInfoDTO);
            $order = $this->pipelines($order)->then(function (Order $order) use ($orderPaidInfoDTO) {
                $this->setPaid($order, $orderPaidInfoDTO);
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
     * @param Order                 $order
     * @param OrderPaidInfoDTO|null $orderPaidInfoDTO
     *
     * @return Order
     */
    protected function setPaid(Order $order, ?OrderPaidInfoDTO $orderPaidInfoDTO = null) : Order
    {
        $order->payment_status  = PaymentStatusEnum::PAID;
        $order->payment_time    = $orderPaidInfoDTO->paymentTime ?? now();
        $order->payment_type    = $orderPaidInfoDTO->paymentType;
        $order->payment_id      = $orderPaidInfoDTO->paymentId;
        $order->payment_channel = $orderPaidInfoDTO->paymentChannel;
        $order->products->each(function (OrderProduct $product) {
            $product->payment_status = PaymentStatusEnum::PAID;
            $product->payment_time   = now();
        });
        return $order;
    }


}
