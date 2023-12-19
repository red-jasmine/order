<?php

namespace RedJasmine\Order\Enums\Orders\Types;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货类型
 */
enum ShippingTypeEnums: string
{
    use EnumsHelper;

    case EXPRESS = 'express'; // 物流快递

    case CARD_KEY = 'card'; // 卡密发货

    case  VIRTUAL = 'virtual';  // 虚拟发货

    public static function names() : array
    {

        return [
            self::EXPRESS->value  => '物流快递',
            self::CARD_KEY->value => '卡密',
            self::VIRTUAL->value  => '虚拟',

        ];

    }

}
