<?php

namespace RedJasmine\Order\Infrastructure\Repositories\Eloquent;


use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use Throwable;

class OrderRepository implements OrderRepositoryInterface
{

    public function find(int $id) : Order
    {
        // with([ 'products',
        //        'products.info',
        //        'info',
        //        'logistics',
        //        'payments' ])


       return  Order::findOrFail($id);


    }

    /**
     * @param \RedJasmine\Order\Domain\Models\Order $order
     *
     * @return Order
     * @throws Throwable
     */
    public function store(Order $order) : Order
    {
        try {
            DB::beginTransaction();

            $order->push();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }

        return $order;
    }

    /**
     * @param \RedJasmine\Order\Domain\Models\Order $order
     *
     * @return void
     * @throws Throwable
     */
    public function update(Order $order) : void
    {
        try {
            DB::beginTransaction();
            $order->push();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }

    }


}
