<?php

namespace RedJasmine\Order\Domains\Order\Domain\Models\ValueObjects;

use RedJasmine\Support\Data\Data;

class Progress extends Data
{

    public ?int $progress = null;

    public ?int $progressTotal = null;

}
