<?php

namespace RedJasmine\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Domain\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Domain\OrderFactory;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Services\OrderShippingService;

class OrderShippingLogisticsCommandHandler extends AbstractOrderCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {
        parent::__construct($orderRepository);
    }


    public function execute(OrderShippingLogisticsCommand $command) : void
    {

        $order                                = $this->find($command->id);
        $orderLogistics                       = app(OrderFactory::class)->createOrderLogistics();
        $orderLogistics->shippable_id         = $order->id;
        $orderLogistics->seller               = $order->seller;
        $orderLogistics->buyer                = $order->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
        $orderLogistics->order_product_id     = $command->orderProducts;
        $orderLogistics->express_company_code = $command->expressCompanyCode;
        $orderLogistics->express_no           = $command->expressNo;
        $orderLogistics->status               = $command->status;
        $orderLogistics->shipping_time        = now();


        $this->handle(
            execute: fn() => $this->orderShippingService->logistics($order, $command->isSplit, $orderLogistics),
            persistence: fn() => $this->orderRepository->update($order)
        );
    }


}
