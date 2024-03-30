<?php

namespace RedJasmine\Order\Services\Order\Actions;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Data\OrderCancelData;
use RedJasmine\Support\Exceptions\AbstractException;

class OrderCancelAction extends AbstractOrderAction
{


    protected bool $lockForUpdate = true;


    protected ?string $dataClass = OrderCancelData::class;

    /**
     * @param int                   $id
     * @param OrderCancelData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderCancelData|array $data = []) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }


    /**
     * 订单状态
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = [
        OrderStatusEnum::WAIT_BUYER_PAY,
    ];


    protected function fill(array $data) : ?Model
    {
        $this->model->cancel_reason = $data['cancel_reason'] ?? '';
        return $this->model;

    }


    protected function handle() : Order
    {

        $order               = $this->model;
        $order->order_status = OrderStatusEnum::CANCEL;
        $order->close_time   = now();
        $order->products->each(function (OrderProduct $product) use ($order) {
            $product->order_status = OrderStatusEnum::CANCEL;
            $product->close_time   = $order->close_time;
        });
        $order->push();

        return $order;
    }

}
