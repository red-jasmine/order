<?php

namespace RedJasmine\Order\Domain\Order\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum RateStatusEnum: string
{
    use EnumsHelper;

    case WAIT_RATE = 'wait_rate';
    case RATED = 'rated';

    public static function labels() : array
    {
        return [
            self::WAIT_RATE->value => '待评价',
            self::RATED->value     => '已评价'

        ];
    }
}
