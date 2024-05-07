<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Refund;

use RedJasmine\Support\Data\Data;

class RefundAgreeRefundCommand extends Data
{
    public int $rid; // 退款单ID

    /**
     * 退款金额
     * @var string|float|int|null
     */
    public string|float|int|null $amount = null;
}
