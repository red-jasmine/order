<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Support\DataTransferObjects\Data;

class OrderRemarksDTO extends Data
{
    public string $type;
    public string $text;

}
