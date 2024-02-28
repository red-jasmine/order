<?php

namespace RedJasmine\Order\Actions\Others;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\DataTransferObjects\Others\OrderRemarksDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Support\Exceptions\AbstractException;

class OrderSellerRemarksAction extends AbstractOrderAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.sellerRemarks';

    /**
     * @param int             $id
     * @param OrderRemarksDTO $DTO
     *
     * @return Order
     * @throws AbstractException
     */
    public function execute(int $id, OrderRemarksDTO $DTO) : Order
    {
        $order = $this->service->find($id);
        $order->setDTO($DTO);
        $this->pipelines($order);
        $this->pipeline->before();
        try {
            DB::beginTransaction();
            $order = $this->pipeline->then(fn(Order $order) => $this->remarks($order, $DTO));
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();

        return $order;
    }


    public function remarks(Order $order, OrderRemarksDTO $DTO) : Order
    {
        $order->info->seller_remarks = $DTO->remarks;
        $order->push();
        return $order;
    }

}
