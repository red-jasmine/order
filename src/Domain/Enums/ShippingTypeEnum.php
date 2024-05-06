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

    case EXPRESS = 'express'; // 物流快递

    case  VIRTUAL = 'virtual';  // 虚拟发货

    case CDK = 'cdk'; // 卡密

    case CITY_DELIVERY = 'city_delivery'; // 同城配送


    public static function labels() : array
    {
        return [
            self::EXPRESS->value       => '快递',
            self::CDK->value           => '卡密',
            self::VIRTUAL->value       => '虚拟',
            self::CITY_DELIVERY->value => '同城配送',
        ];

    }




}
