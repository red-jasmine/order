<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Events\Refunds\RefundCancelledEvent;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;
use Throwable;

/**
 * 取消
 */
class RefundCancelAction extends AbstractRefundAction
{
    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.cancel';


    /**
     *允许的 退款类型
     * @var array|null
     */
    protected ?array $allowRefundType = null;

    protected ?array $allowRefundStatus = [
        RefundStatusEnum::WAIT_SELLER_AGREE,
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
     * @param int $id
     *
     * @return mixed
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
            $orderRefund = $this->pipeline->then(
                fn(OrderRefund $orderRefund) => $this->cancel($orderRefund)
            );
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();
        RefundCancelledEvent::dispatch($orderRefund);
        return $orderRefund;
    }


    /**
     * @param OrderRefund $orderRefund
     *
     * @return OrderRefund
     */
    public function cancel(OrderRefund $orderRefund) : OrderRefund
    {
        $orderRefund->refund_status               = RefundStatusEnum::REFUND_CLOSED;
        $orderRefund->end_time                    = now();
        $orderRefund->updater                     = $this->service->getOperator();
        $orderRefund->orderProduct->refund_status = RefundStatusEnum::REFUND_CLOSED;
        $orderRefund->push();
        return $orderRefund;
    }
}
