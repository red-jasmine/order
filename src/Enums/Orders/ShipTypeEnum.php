<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货类型
 */
enum ShipTypeEnum: string
{
    use EnumsHelper;

    case EXPRESS = 'express'; // 物流快递

    case CDK = 'cdk'; // 卡密发货

    case  VIRTUAL = 'virtual';  // 虚拟发货

    public static function labels() : array
    {

        return [
            self::EXPRESS->value => '物流快递',
            self::CDK->value     => '卡密',
            self::VIRTUAL->value => '虚拟',

        ];

    }

}
