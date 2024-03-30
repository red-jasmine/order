<?php

namespace RedJasmine\Order\Actions;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\OrderSplitProductDTO;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RateStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Events\Orders\OrderPaidEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Exceptions\AbstractException;

class OrderConfirmAction extends AbstractOrderAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.confirm';

    /**
     * 订单状态
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = [
        OrderStatusEnum::WAIT_SELLER_SEND_GOODS,
        OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS,
    ];

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = [
        PaymentStatusEnum::PAID,
        PaymentStatusEnum::NO_PAYMENT,
    ];


    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = [
        ShippingStatusEnum::SHIPPED,
        ShippingStatusEnum::PART_SHIPPED,
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
     * 确认订单
     *
     * @param int                  $id
     * @param OrderSplitProductDTO $DTO
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderSplitProductDTO $DTO) : Order
    {
        try {
            DB::beginTransaction();
            $order = $this->service->findLock($id);
            $this->isAllow($order);
            $order->setDTO($DTO);
            $order = $this->pipelines($order)->then(function (Order $order) use ($DTO) {
                $this->confirm($order, $DTO);
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


        return $order;
    }


    public function confirm(Order $order, OrderSplitProductDTO $DTO) : Order
    {
        // 如果 当前订单是部分发货 那么只能确认 已经发货的 商品
        $order->products
            ->where('order_status', OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS)
            ->where('shipping_status', ShippingStatusEnum::SHIPPED)
            ->each(function (OrderProduct $orderProduct) use ($order, $DTO) {
                if ($order->shipping_status === ShippingStatusEnum::SHIPPED || in_array($orderProduct->id, $DTO->orderProducts, true)) {
                    $this->isAllowConfirmOrderProduct($orderProduct);
                    $orderProduct->order_status = OrderStatusEnum::FINISHED;
                    $orderProduct->rate_status  = RateStatusEnum::WAIT_RATE;
                    $orderProduct->end_time     = now();
                    $orderProduct->updater      = $this->service->getOperator();
                }
            });
        // 有效商品  未退款 TODO
        $finishedCount = $order->products
            ->where('order_status', OrderStatusEnum::FINISHED)
            ->count();
        // 如果都确认完成 那么就标记完成
        if ($finishedCount === $order->products->count()) {
            $order->order_status = OrderStatusEnum::FINISHED;
            $order->end_time     = now();
        }

        return $order;
    }


    public function isAllowConfirmOrderProduct(OrderProduct $orderProduct) : bool
    {

        return true;
    }
}
