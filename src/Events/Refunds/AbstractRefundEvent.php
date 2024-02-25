<?php

namespace RedJasmine\Order\Events\Refunds;

use Illuminate\Foundation\Events\Dispatchable;
use RedJasmine\Order\Models\OrderRefund;

abstract class AbstractRefundEvent
{

    use Dispatchable;


    protected OrderRefund $orderRefund;

    public function __construct(OrderRefund $orderRefund)
    {
        $this->orderRefund = $orderRefund;
    }

}
