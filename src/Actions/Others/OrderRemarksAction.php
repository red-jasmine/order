<?php

namespace RedJasmine\Order\Actions\Others;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\DataTransferObjects\Others\OrderRemarksDTO;
use RedJasmine\Order\Enums\Others\RemarkFormEnum;
use RedJasmine\Order\Events\Others\OrderProductProgressUpdateEvent;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\OrderService;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * 订单备注
 */
class OrderRemarksAction extends AbstractOrderAction
{


    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.remarks';

    protected ?OrderService $service;

    /**
     * @param int             $id
     * @param OrderRemarksDTO $DTO
     *
     * @return OrderProduct
     * @throws AbstractException
     */
    public function execute(int $id, OrderRemarksDTO $DTO) : OrderProduct
    {
        $orderProduct = $this->service->find($id);
        $orderProduct->setDTO($DTO);
        $this->pipelines($orderProduct);
        $this->pipeline->before();
        try {
            DB::beginTransaction();
            $orderProduct = $this->pipeline->then(fn(OrderProduct $orderProduct) => $this->remarks($orderProduct, $DTO));
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();

        return $orderProduct;
    }

    public function remarks(OrderProduct $orderProduct, OrderRemarksDTO $DTO) : OrderProduct
    {
        switch ($DTO->form) {
            case RemarkFormEnum::SELLER:
                $orderProduct->info->seller_remarks = $DTO->remarks;
                break;
            case RemarkFormEnum::BUYER:
                $orderProduct->info->buyer_remarks = $DTO->remarks;
                break;
        }
        $orderProduct->save();
        return $orderProduct;
    }

}
