<?php

namespace RedJasmine\Order\Application\Order\Services\Handlers;

use Exception;
use RedJasmine\Order\Application\Order\Data\OrderData;
use RedJasmine\Order\Application\Order\Mappers\OrderMapper;

use RedJasmine\Order\Domains\Order\Application\Services\Commands\OrderCreateCommand;
use RedJasmine\Order\Domains\Order\Domain\OrderFactory;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;

class OrderCreateCommanHandler
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }


    public function execute(OrderCreateCommand $data) : OrderData
    {
        // 1、业务验证
        //$orderModel = $this->pipelines->send($data)->call('validate', fn() => $this->validate());
        // 2、数据填充 DTO to Model
        $order          = app(OrderFactory::class)->createOrder();
        $orderModel          = app(OrderMapper::class)->fromData($data);
        $orderModel->creator = null;// 设置操作人
        $orderModel->create();
        // 3、持久化
        $orderModel = $this->orderRepository->store($orderModel);
        $orderModel->dispatch();
        return OrderData::from($orderModel);
        // 4、返回after() ... 可以对修改进行修改
        return $this->pipelines->send($orderModel)->call('after', fn() => OrderData::from($orderModel));

    }


}
