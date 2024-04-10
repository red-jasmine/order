<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers;


use RedJasmine\Order\Domains\Order\Application\Data\OrderData;
use RedJasmine\Order\Domains\Order\Application\Mappers\OrderAddressMapper;
use RedJasmine\Order\Domains\Order\Application\Mappers\OrderMapper;
use RedJasmine\Order\Domains\Order\Application\Mappers\OrderProductMapper;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Domains\Order\Domain\OrderFactory;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;

class OrderCreateCommandHandler
{
    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }


    public function execute(OrderCreateCommand $data) : OrderData
    {
        // 1、业务验证 TODO
        //$orderModel = $this->pipelines->send($data)->call('validate', fn() => $this->validate());
        // 2、领域模型
        $order = app(OrderFactory::class)->createOrder();
        app(OrderMapper::class)->fromData($data, $order);

        foreach ($data->products as $productData) {
            $product = app(OrderFactory::class)->createOrderProduct();
            app(OrderProductMapper::class)->fromData($productData, $product);
            $order->addProduct($product);
        }

        if ($data->address) {
            $address = app(OrderFactory::class)->createOrderAddress();
            app(OrderAddressMapper::class)->fromData($data->address, $address);
            $order->setAddress($address);
        }
        $order->create();

        // 3、持久化
        $this->orderRepository->store($order);

        // 5、转换成 DTO
        return app(OrderMapper::class)->fromModel($order);

    }


}
