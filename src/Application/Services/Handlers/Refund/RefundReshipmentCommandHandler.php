<?php

namespace RedJasmine\Order\Application\Services\Handlers\Refund;

use Exception;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReshipmentCommand;
use RedJasmine\Order\Domain\Models\Enums\Logistics\LogisticsShippableTypeEnum;
use RedJasmine\Order\Domain\Models\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Domain\Models\OrderLogistics;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class RefundReshipmentCommandHandler extends AbstractRefundCommandHandler
{


    /**
     * @param RefundReshipmentCommand $command
     *
     * @return void
     * @throws Exception|Throwable
     */
    public function handle(RefundReshipmentCommand $command) : void
    {


        $this->beginDatabaseTransaction();

        try {
            $refund                               = $this->find($command->rid);
            $orderLogistics                       = OrderLogistics::newModel();
            $orderLogistics->shippable_type       = LogisticsShippableTypeEnum::REFUND;
            $orderLogistics->shippable_id         = $refund->id;
            $orderLogistics->seller_type          = $refund->seller_type;
            $orderLogistics->seller_id            = $refund->seller_id;
            $orderLogistics->buyer_type           = $refund->buyer_type;
            $orderLogistics->buyer_id             = $refund->buyer_id;
            $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
            $orderLogistics->order_product_id     = [ $refund->order_product_id ];
            $orderLogistics->express_company_code = $command->expressCompanyCode;
            $orderLogistics->express_no           = $command->expressNo;
            $orderLogistics->status               = $command->status;
            $orderLogistics->shipping_time        = now();
            $refund->reshipment($orderLogistics);
            $this->refundRepository->update($refund);

            $this->commitDatabaseTransaction();
        } catch (AbstractException $exception) {
            $this->rollBackDatabaseTransaction();
            throw  $exception;
        } catch (Throwable $throwable) {
            $this->rollBackDatabaseTransaction();
            throw  $throwable;
        }
    }

}
