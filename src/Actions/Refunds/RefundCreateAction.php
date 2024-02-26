<?php

namespace RedJasmine\Order\Actions\Refunds;

use Exception;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Actions\AbstractOrderProductAction;
use RedJasmine\Order\DataTransferObjects\Refund\OrderProductRefundDTO;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Refund\RefundPhaseEnum;
use RedJasmine\Order\Enums\Refund\RefundTypeEnum;
use RedJasmine\Order\Events\Refunds\RefundCreatedEvent;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Exceptions\RefundException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Order\Services\RefundService;


/**
 * 申请退款
 */
class RefundCreateAction extends AbstractOrderProductAction
{

    protected ?RefundService $service;


    /**
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = null;

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = null;


    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = null;


    /**
     * @var array|null|RefundStatusEnum[]
     */
    protected ?array $allowRefundStatus = [
        null,
        RefundStatusEnum::SELLER_REFUSE_BUYER,
        RefundStatusEnum::REFUND_CLOSED,
        RefundStatusEnum::REFUND_SUCCESS,
    ];


    /**
     * @param OrderProduct $orderProduct
     *
     * @return bool
     * @throws OrderException
     */
    public function isAllow(OrderProduct $orderProduct) : bool
    {
        $this->allowStatus($orderProduct);
        // 不能是 已经关闭的订单
        return true;
    }

    /**
     * @param int                   $id
     * @param OrderProductRefundDTO $DTO
     *
     * @return OrderRefund
     * @throws \Throwable
     */
    public function execute(int $id, OrderProductRefundDTO $DTO) : OrderRefund
    {
        // TODO 最大拒绝次数后 不可再创建
        try {
            DB::beginTransaction();
            $orderProduct = $this->service->orderService->findOrderProductLock($id);
            $orderProduct->setDTO($DTO);
            $pipelines = $this->pipelines($orderProduct);
            $this->validate($orderProduct, $DTO);
            $pipelines->before();
            $orderRefund = $pipelines->then(fn($orderProduct) => $this->refundCreate($orderProduct, $DTO));
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $pipelines->after();

        RefundCreatedEvent::dispatch($orderRefund);
        return $orderRefund;

    }


    /**
     * @param OrderProduct          $orderProduct
     * @param OrderProductRefundDTO $DTO
     *
     * @return void
     * @throws RefundException
     */
    public function validate(OrderProduct $orderProduct, OrderProductRefundDTO $DTO) : void
    {
        try {
            // 1、状态验证
            $this->isAllow($orderProduct);
        } catch (\Throwable $throwable) {
            throw new RefundException('当前状态不支持退款');
        }
        // 2、如果是 【退款、退货退款】 已退款金额需要小于实际支付金额
        $this->validateRefundAmount($orderProduct, $DTO);
        // 3、
    }


    /**
     * @param OrderProduct          $orderProduct
     * @param OrderProductRefundDTO $DTO
     *
     * @return void
     * @throws RefundException
     */
    protected function validateRefundAmount(OrderProduct $orderProduct, OrderProductRefundDTO $DTO) : void
    {
        // 如果是 退款  那么 填写的退款金额必须
        $DTO->refundAmount;


        if (in_array(
            $DTO->refundType, [
            RefundTypeEnum::REFUND_ONLY,
            RefundTypeEnum::RETURN_GOODS_REFUND,
        ],  true
        )) {

            $orderProductMaxRefundAmount = $this->service->orderService->getOrderProductMaxRefundAmount($orderProduct);

            $currentRefundMaxAmount = $this->getCurrentRefundMaxAmount($orderProduct, $DTO);
            $DTO->refundAmount      = $DTO->refundAmount ?? $currentRefundMaxAmount;
            // 如果是 最后一个 退款商品
            if (bccomp($currentRefundMaxAmount, ($DTO->refundAmount ?? $currentRefundMaxAmount), 2) < 0) {
                throw new RefundException("超过最大退款金额 {$currentRefundMaxAmount} ");
            }
            // 如果 大于 商品最大退款 金额  那么就需要加上运费
            if (bccomp($DTO->refundAmount, $orderProductMaxRefundAmount, 2) > 0) {
                $DTO->refundAmount = $currentRefundMaxAmount;
                $DTO->setFreightAmount(bcsub($currentRefundMaxAmount, $orderProductMaxRefundAmount, 2));
            }


        } else {
            $DTO->refundAmount = 0;
        }

    }

    /**
     * 获取当前最大能退款的金额
     *
     * @param OrderProduct          $orderProduct
     * @param OrderProductRefundDTO $DTO
     *
     * @return string
     */
    protected function getCurrentRefundMaxAmount(OrderProduct $orderProduct, OrderProductRefundDTO $DTO) : string
    {
        // 当前商品最大退款 金额
        $orderProductMaxRefundAmount = $this->service->orderService->getOrderProductMaxRefundAmount($orderProduct);

        // 当且仅仅在 订单  在未发货的情况下
        if ($orderProduct->order->shipping_status === ShippingStatusEnum::WAIT_SEND) {
            // 如果是最后一个商品申请那么就需要加上 运费 TODO
            $isLastRefundOrderProduct = true;
            $otherProducts            = $orderProduct->order->products->where('id', '<>', $orderProduct->id)->values();
            foreach ($otherProducts as $otherProduct) {
                if (!$this->isRefundOrderProduct($otherProduct)) {
                    $isLastRefundOrderProduct = false;
                }
            }
            if ($isLastRefundOrderProduct) {
                $orderProductMaxRefundAmount = bcadd($orderProductMaxRefundAmount, $orderProduct->order->freight_amount, 2);
            }
        }


        return $orderProductMaxRefundAmount;
    }


    protected function isRefundOrderProduct(OrderProduct $orderProduct) : bool
    {
        // 还有正常履行的单
        // 全款退了
        if (bccomp($orderProduct->refund_amount, $orderProduct->divided_payment_amount, 2,) >= 0) {
            return true;
        }
        // 退款售后申请中
        if (!in_array($orderProduct->refund_status,
            [
                RefundStatusEnum::REFUND_CLOSED,
                null,
            ], true)) {
            return true;
        }
        return false;
    }

    /**
     * @param OrderProduct          $orderProduct
     * @param OrderProductRefundDTO $DTO
     *
     * @return OrderRefund
     * @throws Exception
     */
    public function refundCreate(OrderProduct $orderProduct, OrderProductRefundDTO $DTO) : OrderRefund
    {
        $orderRefund                         = new OrderRefund();
        $orderRefund->id                     = $this->service->buildID();
        $orderRefund->order_id               = $orderProduct->order_id;
        $orderRefund->order_product_id       = $orderProduct->id;
        $orderRefund->seller                 = $orderProduct->seller;
        $orderRefund->buyer                  = $orderProduct->buyer;
        $orderRefund->shipping_type          = $orderProduct->shipping_type;
        $orderRefund->order_product_type     = $orderProduct->order_product_type;
        $orderRefund->title                  = $orderProduct->title;
        $orderRefund->sku_name               = $orderProduct->sku_name;
        $orderRefund->image                  = $orderProduct->image;
        $orderRefund->product_type           = $orderProduct->product_type;
        $orderRefund->product_id             = $orderProduct->product_id;
        $orderRefund->sku_id                 = $orderProduct->sku_id;
        $orderRefund->category_id            = $orderProduct->category_id;
        $orderRefund->seller_category_id     = $orderProduct->seller_category_id;
        $orderRefund->outer_id               = $orderProduct->outer_id;
        $orderRefund->outer_sku_id           = $orderProduct->outer_sku_id;
        $orderRefund->barcode                = $orderProduct->barcode;
        $orderRefund->num                    = $orderProduct->num;
        $orderRefund->price                  = $orderProduct->price;
        $orderRefund->cost_price             = $orderProduct->cost_price;
        $orderRefund->amount                 = $orderProduct->amount;
        $orderRefund->tax_amount             = $orderProduct->tax_amount;
        $orderRefund->payment_amount         = $orderProduct->payment_amount;
        $orderRefund->divided_payment_amount = $orderProduct->divided_payment_amount;

        $orderRefund->phase           = $this->getRefundPhase($orderProduct);
        $orderRefund->refund_status   = $DTO->refundType === RefundTypeEnum::REFUND_ONLY ? RefundStatusEnum::WAIT_SELLER_AGREE : RefundStatusEnum::WAIT_SELLER_AGREE_RETURN;
        $orderRefund->refund_type     = $DTO->refundType;
        $orderRefund->freight_amount  = $DTO->getFreightAmount(); // 如果是最后一笔退款单  那么就需要加上运费 并且还没有发货的情况下
        $orderRefund->refund_amount   = $DTO->refundAmount; //
        $orderRefund->has_good_return = $this->hasGoodReturn($DTO);
        $orderRefund->good_status     = $DTO->goodStatus;
        $orderRefund->reason          = $DTO->reason;
        $orderRefund->description     = $DTO->description;
        $orderRefund->images          = $DTO->images;
        $orderRefund->created_time    = now();
        $orderProduct->refund_id      = $orderRefund->id; // 最新售后单ID
        $orderProduct->refund_status  = $orderRefund->refund_status; // 同步退款单状态
        $orderRefund->creator         = $this->service->getOperator();
        $orderRefund->save();
        $orderProduct->save();
        return $orderRefund;
    }


    public function hasGoodReturn(OrderProductRefundDTO $DTO) : bool
    {
        if (in_array(
            $DTO->refundType, [
            RefundTypeEnum::RETURN_GOODS_REFUND,
            RefundTypeEnum::EXCHANGE_GOODS,
            RefundTypeEnum::SERVICE
        ],  true
        )) {
            return true;
        }
        return false;
    }

    /**
     * 获取退款售后单阶段
     *
     * @param OrderProduct $orderProduct
     *
     * @return RefundPhaseEnum
     */
    protected function getRefundPhase(OrderProduct $orderProduct) : RefundPhaseEnum
    {

        if ($orderProduct->order_status === OrderStatusEnum::FINISHED) {
            RefundPhaseEnum::AFTER_SALE;
        }
        return RefundPhaseEnum::ON_SALE;
    }
}
