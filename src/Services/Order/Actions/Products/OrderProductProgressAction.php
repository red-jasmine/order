<?php

namespace RedJasmine\Order\Services\Order\Actions\Products;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderProductAction;
use RedJasmine\Order\Services\Order\Data\OrderProductProgressData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderProduct             $model
 * @property OrderProductProgressData $data
 */
class OrderProductProgressAction extends AbstractOrderProductAction
{


    protected ?string $modelClass = OrderProduct::class;

    protected ?string $dataClass = OrderProductProgressData::class;


    /**
     * @param int                            $id
     * @param OrderProductProgressData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderProductProgressData|array $data) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }


    public function handle() : Model
    {
        $orderProduct = $this->model;
        $DTO          = $this->data;
        if ($DTO->isAppend) {
            $orderProduct->increment('progress', $DTO->progress, [ 'progress_total' => $DTO->progressTotal ?? $orderProduct->progress_total,
            ]);
        } else {
            $orderProduct->progress       = $DTO->progress ?? $orderProduct->progress;
            $orderProduct->progress_total = $DTO->progressTotal ?? $orderProduct->progress_total;
        }

        $orderProduct->push();
        return $orderProduct;
    }

}
