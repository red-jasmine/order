<?php

namespace RedJasmine\Order\Enums\Orders\Types;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单类型
 */
enum OrderTypeEnums: string
{
    use EnumsHelper;

    case  FIXED = 'fixed'; // 一口价

    public static function names() : array
    {
        return [
            self::FIXED->value => '一口价',
        ];
    }


}
