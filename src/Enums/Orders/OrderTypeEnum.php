<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单类型
 */
enum OrderTypeEnum: string
{
    use EnumsHelper;

    case  MALL = 'MALL'; // 一口价

    public static function names() : array
    {
        return [
            self::MALL->value => '商城',
        ];
    }


}
