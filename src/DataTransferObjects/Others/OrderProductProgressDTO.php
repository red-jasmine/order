<?php

namespace RedJasmine\Order\DataTransferObjects\Others;

use RedJasmine\Support\DataTransferObjects\Data;

class OrderProductProgressDTO extends Data
{

    public ?int $progress;

    public ?int $progressTotal;

}
