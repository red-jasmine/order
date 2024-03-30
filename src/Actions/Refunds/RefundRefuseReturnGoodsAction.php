<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Refund\RefundRefuseDTO;
use RedJasmine\Order\Services\Order\Enums\RefundStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Order\Events\Refunds\RefundRefusedEvent;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class RefundRefuseReturnGoodsAction extends AbstractRefundAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.refuseReturnGoods';

    protected ?array $allowRefundType = [

        RefundTypeEnum::RETURN_GOODS_REFUND,
        RefundTypeEnum::EXCHANGE_GOODS,
        RefundTypeEnum::SERVICE,
    ];

    protected ?array $allowRefundStatus = [
        RefundStatusEnum::WAIT_SELLER_AGREE_RETURN,
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
     * @param RefundRefuseDTO|null $DTO
     *
     * @return OrderRefund
     * @throws Throwable
     */
    public function execute(int $id, ?RefundRefuseDTO $DTO = null) : OrderRefund
    {
        try {
            DB::beginTransaction();
            $orderRefund = $this->service->findLock($id);
            $orderRefund->setDTO($DTO);
            $this->isAllow($orderRefund);
            $this->pipelines($orderRefund);
            $this->pipeline->before();
            $this->pipeline->then(fn(OrderRefund $orderRefund) => $this->refuseReturnGoods($orderRefund, $DTO));
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();

        RefundRefusedEvent::dispatch($orderRefund);
        return $orderRefund;

    }

    public function refuseReturnGoods(OrderRefund $orderRefund, ?RefundRefuseDTO $DTO = null) : OrderRefund
    {

        $orderRefund->refund_status               = RefundStatusEnum::SELLER_REFUSE_BUYER;
        $orderRefund->refuse_reason               = $DTO->refuseReason;
        $orderRefund->updater                     = $this->service->getOperator();
        $orderRefund->end_time                    = now();
        $orderRefund->orderProduct->refund_status = RefundStatusEnum::SELLER_REFUSE_BUYER;
        $orderRefund->push();

        return $orderRefund;
    }

}
