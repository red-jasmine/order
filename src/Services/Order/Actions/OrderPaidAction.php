<?php

namespace RedJasmine\Order\Services\Order\Actions;


use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Data\OrderPaidInfoData;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderPaidInfoData $data
 */
class OrderPaidAction extends AbstractOrderAction
{

    protected bool $lockForUpdate = true;

    protected ?string $dataClass = OrderPaidInfoData::class;

    protected ?array $forbidOrderStatus = [
        OrderStatusEnum::CANCEL,
        OrderStatusEnum::CLOSED,
        OrderStatusEnum::FINISHED,
    ];

    protected ?array $allowPaymentStatus = [
        null,
        PaymentStatusEnum::WAIT_PAY,
        PaymentStatusEnum::PAYING,
        PaymentStatusEnum::PART_PAY,
    ];


    /**
     * @param int                     $id
     * @param OrderPaidInfoData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderPaidInfoData|array $data = []) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }

    protected function fill(array $data) : ?Model
    {
        $order                  = $this->model;
        $order->payment_time    = $this->data->paymentTime ?? now();
        $order->payment_type    = $this->data->paymentType;
        $order->payment_id      = $this->data->paymentId;
        $order->payment_channel = $this->data->paymentChannel;
        $order->payment_amount  = bcadd($order->payment_amount, $this->data->paymentAmount, 2);
        // TODO 如果支付金额超过了 应付金额应该如何处理？
        // 对于 分开支付的退款应该如何处理
        return $this->model;
    }


    protected function handle() : Order
    {

        $order = $this->model;

        $order->payment_status = PaymentStatusEnum::PART_PAY;

        if (bccomp($order->payment_amount, $order->payable_amount, 2) >= 0) {
            $order->payment_status = PaymentStatusEnum::PAID;
        }
        $order->products->each(function (OrderProduct $product) use ($order) {
            $product->payment_status = $order->payment_status;
            $product->payment_time   = $order->payment_time;
        });

        // 如果支付成功
        if ($order->payment_status === PaymentStatusEnum::PAID) {
            if ($order->order_status === OrderStatusEnum::WAIT_BUYER_PAY) {
                $order->order_status = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
            }
            if ($order->shipping_status === null) {
                $order->shipping_status = ShippingStatusEnum::WAIT_SEND;
            }
            $order->products->each(function (OrderProduct $product) use ($order) {
                if ($product->shipping_status === null) {
                    $product->shipping_status = ShippingStatusEnum::WAIT_SEND;
                }
                if ($product->order_status === OrderStatusEnum::WAIT_BUYER_PAY) {
                    $product->order_status = OrderStatusEnum::WAIT_SELLER_SEND_GOODS;
                }
            });
        }
        $order->push();

        return $order;
    }

}
