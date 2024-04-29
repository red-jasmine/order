<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货类型
 */
enum ShippingTypeEnum: string
{
    use EnumsHelper;

    // 自提
    // 同城配送

    case EXPRESS = 'express'; // 物流快递

    case  VIRTUAL = 'virtual';  // 虚拟发货

    case CDK = 'cdk'; // 卡密

    public static function labels() : array
    {
        return [
            self::EXPRESS->value => '物流快递',
            self::CDK->value     => '卡密',
            self::VIRTUAL->value => '虚拟',

        ];

    }

}
