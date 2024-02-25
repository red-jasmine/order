<?php

namespace RedJasmine\Order\Actions\Refunds;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\Refund\RefundAgreeDTO;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Order\Events\Refunds\RefundAgreedEvent;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderRefund;

class RefundAgreeAction extends AbstractRefundAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.refund.agree';


    protected ?array $allowRefundType = [
        RefundTypeEnum::REFUND_ONLY,
        RefundTypeEnum::RETURN_GOODS_REFUND
    ];

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
     * @param int            $id
     * @param RefundAgreeDTO $DTO
     *
     * @return OrderRefund
     * @throws RefundException
     * @throws \Throwable
     */
    public function execute(int $id, RefundAgreeDTO $DTO) : OrderRefund
    {
        try {
            DB::beginTransaction();
            $orderRefund = $this->service->findLock($id);
            $orderRefund->setDTO($DTO);
            $this->isAllow($orderRefund);
            $pipelines = $this->pipelines($orderRefund);
            $pipelines->before();
            $orderRefund = $pipelines->then(fn(OrderRefund $orderRefund) => $this->agreeRefund($orderRefund, $DTO));

            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }

        RefundAgreedEvent::dispatch($orderRefund);
        return $orderRefund;
    }


    /**
     * 同意退款
     *
     * @param OrderRefund    $orderRefund
     * @param RefundAgreeDTO $DTO
     *
     * @return OrderRefund
     * @throws RefundException
     */
    public function agreeRefund(OrderRefund $orderRefund, RefundAgreeDTO $DTO) : OrderRefund
    {

        $refundAmount = $DTO->refundAmount ?? $orderRefund->refund_amount;
        if (bccomp($refundAmount, $orderRefund->refund_amount, 2) > 0) {
            throw new RefundException('退款金额不能大于最大退款金额');
        }
        $orderRefund->end_time      = now();
        $orderRefund->refund_amount = $refundAmount;
        $orderRefund->refund_status = RefundStatusEnum::REFUND_SUCCESS;
        $orderRefund->remarks       = $DTO->remarks ?? $orderRefund->remarks;
        $orderRefund->updater       = $this->service->getOperator();
        // 同步退款单状态

        $orderRefund->orderProduct->increment('refund_amount', $orderRefund->refund_amount);
        $orderRefund->orderProduct->refund_status = RefundStatusEnum::REFUND_SUCCESS;

        $orderRefund->order->increment('refund_amount', $orderRefund->refund_amount);

        if (bccomp($orderRefund->orderProduct->refund_amount, $orderRefund->orderProduct->divided_discount_amount, 2) > 0) {
            $orderRefund->orderProduct->refund_amount = $orderRefund->orderProduct->divided_payment_amount;
        }
        $orderRefund->orderProduct->refund_time = now();
        if (bccomp($orderRefund->orderProduct->refund_amount, $orderRefund->orderProduct->divided_discount_amount, 2) >= 0) {
            $orderRefund->orderProduct->order_status = OrderStatusEnum::CLOSED;
        }

        if (bccomp($orderRefund->order->refund_amount, $orderRefund->order->payment_amount, 2) >= 0) {
            $orderRefund->order->order_status = OrderStatusEnum::CLOSED;
            $orderRefund->order->close_time   = now();
            $orderRefund->order->refund_time  = now();
        }
        $orderRefund->push();
        return $orderRefund;
    }


}
