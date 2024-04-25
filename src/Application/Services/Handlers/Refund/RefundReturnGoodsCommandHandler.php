<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReturnGoodsCommand;
use RedJasmine\Order\Domain\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Domain\OrderFactory;

class RefundReturnGoodsCommandHandler extends AbstractRefundCommandHandler
{




    public function execute(RefundReturnGoodsCommand $command) : void
    {
        $refund  = $this->find($command->rid);

        $orderLogistics                       = app(OrderFactory::class)->createOrderLogistics();
        $orderLogistics->shippable_type       = 'refund';
        $orderLogistics->shippable_id         = $refund->id;
        $orderLogistics->seller               = $refund->seller;
        $orderLogistics->buyer                = $refund->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::BUYER;
        $orderLogistics->order_product_id     = [ $refund->order_product_id ];
        $orderLogistics->express_company_code = $command->expressCompanyCode;
        $orderLogistics->express_no           = $command->expressNo;
        $orderLogistics->status               = $command->status;
        $orderLogistics->shipping_time        = now();

        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', fn() => $refund->returnGoods($orderLogistics));
        $this->refundRepository->update($refund);
        $this->pipelineManager()->call('executed');


    }

}
