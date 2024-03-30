<?php

namespace RedJasmine\Order\Services\Order\Actions\Others;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Services\Order\Actions\AbstractOrderAction;
use RedJasmine\Support\Exceptions\AbstractException;

class OrderSellerHiddenAction extends AbstractOrderAction
{

    protected bool $processHasData = false;


    /**
     * @param int $id
     *
     * @return mixed
     * @throws OrderException
     * @throws AbstractException
     */
    public function execute(int $id) : mixed
    {
        $this->key = $id;

        return $this->process();
    }


    public function handle() : Model
    {
        $order                   = $this->model;
        $order->is_seller_delete = true;
        $order->push();
        return $order;
    }
}
