<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Support\Data\Data;

class OrderRemarksDTO extends Data
{
    public string $type;
    public string $text;

}
