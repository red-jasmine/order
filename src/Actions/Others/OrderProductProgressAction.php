<?php

namespace RedJasmine\Order\Actions\Others;

use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Actions\AbstractOrderProductAction;
use RedJasmine\Order\DataTransferObjects\Others\OrderProductProgressDTO;
use RedJasmine\Order\Events\Others\OrderProductProgressUpdateEvent;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\OrderService;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class OrderProductProgressAction extends AbstractOrderProductAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.order.productProgress';

    protected ?OrderService $service;

    /**
     * @param int                     $id
     * @param OrderProductProgressDTO $DTO
     *
     * @return OrderProduct
     * @throws AbstractException
     * @throws Throwable
     */
    public function execute(int $id, OrderProductProgressDTO $DTO) : OrderProduct
    {
        $orderProduct = $this->service->findOrderProduct($id);
        $orderProduct->setDTO($DTO);
        $this->pipelines($orderProduct);
        $this->pipeline->before();
        try {
            DB::beginTransaction();
            $orderProduct = $this->pipeline->then(fn(OrderProduct $orderProduct) => $this->progress($orderProduct, $DTO));

            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        $this->pipeline->after();
        if ($orderProduct->isDirty([ 'progress', 'progress_total' ])) {
            OrderProductProgressUpdateEvent::dispatch($orderProduct);
        }
        return $orderProduct;
    }

    public function progress(OrderProduct $orderProduct, OrderProductProgressDTO $DTO) : OrderProduct
    {
        $orderProduct->progress       = $DTO->progress ?? $orderProduct->progress;
        $orderProduct->progress_total = $DTO->progress_total ?? $orderProduct->progress_total;
        $orderProduct->save();
        return $orderProduct;
    }
}
