<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Exceptions\AbstractException;

class RefundAgreeReturnAction extends AbstractRefundAction
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
     * @throws \Throwable
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
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();


        return $orderRefund;

    }


    public function agreeReturn(OrderRefund $orderRefund) : OrderRefund
    {

        return $orderRefund;
    }
}
