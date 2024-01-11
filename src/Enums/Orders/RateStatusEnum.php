<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum RateStatusEnum: string
{
    use EnumsHelper;

    case RATED = 'RATED';

    public static function labels() : array
    {
        return [];
    }
}
