<?php

namespace RedJasmine\Order\Infrastructure\Repositories\Eloquent;

use DB;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Order\Domain\Repositories\RefundRepositoryInterface;
use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class RefundRepository implements RefundRepositoryInterface
{
    /**
     * @param int $rid
     *
     * @return OrderRefund
     */
    public function find(int $rid) : OrderRefund
    {
        return OrderRefund::findOrFail($rid);
    }

    /**
     * @param OrderRefund $orderRefund
     *
     * @return void
     * @throws AbstractException
     * @throws Throwable
     */
    public function store(OrderRefund $orderRefund) : void
    {
        try {
            DB::beginTransaction();
            $orderRefund->push();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }

    }

    /**
     * @param OrderRefund $orderRefund
     *
     * @return void
     * @throws AbstractException
     * @throws Throwable
     */
    public function update(OrderRefund $orderRefund) : void
    {
        try {
            DB::beginTransaction();
            $orderRefund->push();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
    }


}
