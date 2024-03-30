<?php

namespace RedJasmine\Order\Services\Order\Actions\Products;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderProductAction;
use RedJasmine\Order\Services\Order\Data\OrderSellerCustomStatusData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderSellerCustomStatusData $data
 * @property OrderProduct                $model
 */
class OrderProductSellerCustomStatusAction extends AbstractOrderProductAction
{

    protected ?string $modelClass = OrderProduct::class;

    protected ?string $dataClass = OrderSellerCustomStatusData::class;


    /**
     * @param int                               $id
     * @param OrderSellerCustomStatusData|array $data
     *
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    public function execute(int $id, OrderSellerCustomStatusData|array $data) : mixed
    {
        $this->key  = $id;
        $this->data = $data;
        return $this->process();
    }


    public function handle() : Model
    {
        $model                       = $this->model;
        $model->seller_custom_status = $this->data->sellerCustomStatus;
        $model->push();
        return $model;
    }
}
