<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Events\Refunds\RefundAgreeReturnEvent;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class RefundAgreeReturnGoodsAction extends AbstractRefundAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.agreeReturn';


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
     * @param int $id
     *
     * @return OrderRefund
     * @throws RefundException
     * @throws Throwable
     */
    public function execute(int $id) : OrderRefund
    {
        try {
            DB::beginTransaction();
            $orderRefund = $this->service->findLock($id);

            $this->isAllow($orderRefund);

            $this->pipelines($orderRefund);

            $this->pipeline->before();

            $orderRefund = $this->pipeline->then(fn(OrderRefund $orderRefund) => $this->agreeReturn($orderRefund));

            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();
        RefundAgreeReturnEvent::dispatch($orderRefund);
        return $orderRefund;

    }


    /**
     * 同意退货
     *
     * @param OrderRefund $orderRefund
     *
     * @return OrderRefund
     */
    public function agreeReturn(OrderRefund $orderRefund) : OrderRefund
    {
        $orderRefund->refund_status               = RefundStatusEnum::WAIT_BUYER_RETURN_GOODS;
        $orderRefund->updater                     = $this->service->getOperator();
        $orderRefund->orderProduct->refund_status = RefundStatusEnum::WAIT_BUYER_RETURN_GOODS;
        $orderRefund->push();
        return $orderRefund;
    }
}
