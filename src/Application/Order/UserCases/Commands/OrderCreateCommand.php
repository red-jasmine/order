<?php

namespace RedJasmine\Order\Application\Order\UserCases\Commands;

use Exception;
use RedJasmine\Order\Application\Order\Data\OrderData;
use RedJasmine\Order\Application\Order\Mappers\OrderMapper;
use RedJasmine\Order\Domain\Order\Models\Order;
use RedJasmine\Order\Domain\Order\OrderRepositoryInterface;

class OrderCreateCommand
{

    // 指令 含有的

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @param OrderData $data
     *
     * @return OrderData
     * @throws Exception
     */
    public function execute(OrderData $data) : OrderData
    {
        // 1、业务验证
        //$orderModel = $this->pipelines->send($data)->call('validate', fn() => $this->validate());
        // 2、数据填充 DTO to Model
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
