<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum OrderProductTypeEnum: string
{
    use EnumsHelper;

    case GOODS = 'goods'; // 实物

    case VIRTUAL = 'virtual'; // 虚拟

    public static function labels() : array
    {
        return [
            self::GOODS->value   => '实物',
            self::VIRTUAL->value => '虚拟',
        ];
    }
}
