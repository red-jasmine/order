<?php

namespace RedJasmine\Order\Application\UserCases\Commands;

use RedJasmine\Support\Data\Data;

class OrderPayingCommand extends Data
{
    /**
     * @param int    $id     订单ID
     * @param string $amount 金额
     */
    public function __construct(
        public int    $id,
        public string $amount,
        public string $amountType
    )
    {
    }


}
