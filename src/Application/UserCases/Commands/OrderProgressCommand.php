<?php

namespace RedJasmine\Order\Application\UserCases\Commands;

use RedJasmine\Support\Data\Data;

class OrderProgressCommand extends Data
{
    public int $id;

    public int $orderProductId;

    public ?int $progress;

    public ?int $progressTotal;
}
