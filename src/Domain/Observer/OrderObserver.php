<?php

namespace RedJasmine\Order\Domain\Observer;

use RedJasmine\Order\Domain\Models\Order;

class OrderObserver
{
    public function created(Order $order) : void
    {

    }

    public function updated(Order $order) : void
    {
    }

    public function deleted(Order $order) : void
    {
    }

    public function restored(Order $order) : void
    {
    }

    public function progress(Order $order):void
    {

        dd(1);

    }
}
