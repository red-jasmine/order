<?php

namespace RedJasmine\Order\Domain\Models\ValueObjects;

use Exception;

class PromiseServiceSupported
{


    public function __construct(public readonly string $supported = 'unsupported')
    {
        // 允许的值

    }


}
