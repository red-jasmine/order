<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单类型
 */
enum OrderTypeEnum: string
{
    use EnumsHelper;

    case  MALL = 'mall'; // 一口价

    public static function labels() : array
    {
        return [
            self::MALL->value => '商城',
        ];
    }


}
