<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货类型
 */
enum ShippingTypeEnums: string
{
    use EnumsHelper;

    case EXPRESS = 'EXPRESS'; // 物流快递

    case CDK = 'CDK'; // 卡密发货

    case  VIRTUAL = 'VIRTUAL';  // 虚拟发货

    public static function names() : array
    {

        return [
            self::EXPRESS->value => '物流快递',
            self::CDK->value     => '卡密',
            self::VIRTUAL->value => '虚拟',

        ];

    }

}
