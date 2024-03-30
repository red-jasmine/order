<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Refund\RefundReturnGoodsDTO;
use RedJasmine\Order\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Services\Order\Enums\RefundStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Order\Events\Refunds\RefundReturnGoodsEvent;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderLogistics;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

/**
 * 卖家 发货
 */
class RefundSellerReturnGoodsAction extends AbstractRefundAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.sellerReturnGoods';


    protected ?array $allowRefundType = [
        RefundTypeEnum::EXCHANGE_GOODS,
        RefundTypeEnum::SERVICE,
    ];

    protected ?array $allowRefundStatus = [
        RefundStatusEnum::WAIT_SELLER_CONFIRM_GOODS,
    ];


    /**
     * @param OrderRefund $orderRefund
     *
     * @return bool
     * @throws RefundException
     */
    public function isAllow(OrderRefund $orderRefund) : bool
    {
        $this->allowStatus($orderRefund);
        return true;
    }

    /**
     * @param int                  $id
     * @param RefundReturnGoodsDTO $DTO
     *
     * @return OrderRefund
     * @throws AbstractException
     * @throws RefundException
     * @throws Throwable
     */
    public function execute(int $id, RefundReturnGoodsDTO $DTO) : OrderRefund
    {
        try {
            DB::beginTransaction();
            $orderRefund = $this->service->findLock($id);
            $orderRefund->setDTO($DTO);
            $this->isAllow($orderRefund);
            $this->pipelines($orderRefund);
            $this->pipeline->before();
            $this->pipeline->then(fn(OrderRefund $orderRefund) => $this->sellerReturnGoods($orderRefund, $DTO));
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();

        RefundReturnGoodsEvent::dispatch($orderRefund);

        return $orderRefund;

    }


    public function sellerReturnGoods(OrderRefund $orderRefund, RefundReturnGoodsDTO $DTO) : OrderRefund
    {
        $orderRefund->refund_status           = RefundStatusEnum::REFUND_SUCCESS;
        $orderRefund->end_time                = now();
        $orderRefund->updater                 = $this->service->getOperator();
        $orderLogistics                       = new OrderLogistics();
        $orderLogistics->seller               = $orderRefund->seller;
        $orderLogistics->buyer                = $orderRefund->buyer;
        $orderLogistics->shipper              = LogisticsShipperEnum::SELLER;
        $orderLogistics->order_product_id     = [ $orderRefund->order_product_id ];
        $orderLogistics->express_company_code = $DTO->expressCompanyCode;
        $orderLogistics->express_no           = $DTO->expressNo;
        $orderLogistics->status               = $DTO->status;
        $orderLogistics->shipping_time        = now();
        $orderLogistics->creator              = $this->service->getOperator();
        $orderRefund->logistics()->save($orderLogistics);
        $orderRefund->orderProduct->refund_status = RefundStatusEnum::REFUND_SUCCESS;
        $orderRefund->push();
        return $orderRefund;
    }
}
