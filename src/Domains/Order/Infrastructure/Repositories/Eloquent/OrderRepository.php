<?php

namespace RedJasmine\Order\Domains\Order\Infrastructure\Repositories\Eloquent;


use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Domains\Order\Domain\Models\Order;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;
use Throwable;

class OrderRepository implements OrderRepositoryInterface
{

    public function find(int $id) : Order
    {
       return  Order::with([ 'products',
                               'products.info',
                               'info',
                               'logistics',
                               'payments' ])
                      ->findOrFail($id);


    }

    /**
     * @param Order $order
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
     * @param Order $order
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
