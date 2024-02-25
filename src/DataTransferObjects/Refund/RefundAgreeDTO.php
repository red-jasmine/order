<?php

namespace RedJasmine\Order\DataTransferObjects\Refund;

use RedJasmine\Support\DataTransferObjects\Data;

class RefundAgreeDTO extends Data
{

    /**
     * 退款金额
     * @var string|int|float|null
     */
    public string|int|float|null $refundAmount = null;


    public ?string $remarks = null;

}
