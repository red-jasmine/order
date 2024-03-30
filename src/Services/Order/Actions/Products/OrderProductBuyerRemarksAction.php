<?php

namespace RedJasmine\Order\Services\Order\Actions\Products;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderProductAction;
use RedJasmine\Order\Services\Order\Data\OrderProductProgressData;
use RedJasmine\Order\Services\Order\Data\OrderRemarksData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderProduct     $model
 * @property OrderRemarksData $data
 */
class OrderProductBuyerRemarksAction extends AbstractOrderProductAction
{


    protected ?string $modelClass = OrderProduct::class;

    protected ?string $dataClass = OrderRemarksData::class;


    /**
     * @param int                    $id
     * @param OrderRemarksData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderRemarksData|array $data) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }


    public function handle() : Model
    {
        $orderProduct = $this->model;

        if ($this->data->isAppend) {
            $orderProduct->info->buyer_remarks .= "\n" . $this->data->remarks;
        } else {
            $orderProduct->info->buyer_remarks = $this->data->remarks;
        }
        $orderProduct->push();
        return $orderProduct;
    }

}
