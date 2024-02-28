<?php

namespace RedJasmine\Order\DataTransferObjects\Others;

use RedJasmine\Order\Enums\Others\OrderActionFromEnum;
use RedJasmine\Support\DataTransferObjects\Data;

class OrderOtherActionDTO extends Data
{
    public OrderActionFromEnum $from;
}
