<?php

namespace RedJasmine\Order\DataTransferObjects\Refund;

use RedJasmine\Support\DataTransferObjects\Data;

class RefundRefuseDTO extends Data
{

    public ?string $refuseReason = null;

}
