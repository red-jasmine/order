<?php

namespace RedJasmine\Order\Services\Order\Data;

use RedJasmine\Support\Data\Data;

class OrderProductProgressData extends Data
{


    /**
     * 进度累加
     * @var bool
     */
    public      $isAppend = false;
    public ?int $progress;

    public ?int $progressTotal;

}
