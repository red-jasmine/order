<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Refund;

use RedJasmine\Support\Data\Data;

class RefundRejectReturnGoodsCommand extends Data
{
    public int $rid; // 退款单ID

    public string $reason;
}
