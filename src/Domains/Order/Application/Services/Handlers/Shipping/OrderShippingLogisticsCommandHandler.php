<?php

namespace RedJasmine\Order\Domains\Order\Application\Services\Handlers\Shipping;

use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Domains\Order\Domain\OrderFactory;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domains\Order\Domain\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Domains\Order\Domain\Services\OrderShippingService;

class OrderShippingLogisticsCommandHandler
{

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderShippingService     $orderShippingService
    )
    {
    }


    public function execute(OrderShippingLogisticsCommand $command) : void
    {

        $order = $this->orderRepository->find($command->id);

        $orderLogistics                       = app(OrderFactory::class)->createOrderLogistics();
        $orderLogistics->shippable_type       = 'order';
        $orderLogistics->shippable_id         = $order->id;
        $orderLogistics->seller               = $order->seller;
        $orderLogistics->buyer                = $order->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
        $orderLogistics->order_product_id     = $command->orderProducts;
        $orderLogistics->express_company_code = $command->expressCompanyCode;
        $orderLogistics->express_no           = $command->expressNo;
        $orderLogistics->status               = $command->status;
        $orderLogistics->shipping_time        = now();

        $this->orderShippingService->logistics($order, $command->isSplit, $orderLogistics);

        $this->orderRepository->update($order);

        $order->dispatchEvents();
    }


}
