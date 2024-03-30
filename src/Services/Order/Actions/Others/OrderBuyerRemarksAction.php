<?php

namespace RedJasmine\Order\Services\Order\Actions\Others;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderAction;
use RedJasmine\Order\Services\Order\Data\OrderRemarksData;
use RedJasmine\Support\Exceptions\AbstractException;

/**
 * @property OrderRemarksData $data
 */
class OrderBuyerRemarksAction extends AbstractOrderAction
{

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
        $order = $this->model;
        if ($this->data->isAppend) {
            $order->info->buyer_remarks .= "\n" . $this->data->remarks;
        } else {
            $order->info->buyer_remarks = $this->data->remarks;
        }
        $order->push();
        return $order;
    }
}
