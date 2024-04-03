<?php

namespace RedJasmine\Order\Domains\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 卡密状态
 */
enum OrderCardKeyStatusEnum: string
{
    use EnumsHelper;

    case   SHIPPED = 'shipped';

    public static function labels() : array
    {
        return [
            self::SHIPPED->value => '已发货'
        ];
    }
}
