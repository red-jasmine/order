<?php

namespace RedJasmine\Order\Services\Order\Data;

use RedJasmine\Support\Data\Data;

class OrderRemarksData extends Data
{

    /**
     * 是否为追加
     * @var bool
     */
    public bool $isAppend = false;

    public ?string $remarks;

}
