<?php

namespace RedJasmine\Order\Application\Services\Refunds\Commands;

use RedJasmine\Support\Data\Data;

class RefundStarCommand extends Data
{
    public int $id;

    public ?int $star = null;


}
