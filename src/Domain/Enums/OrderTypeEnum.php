<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单类型
 */
enum OrderTypeEnum: string
{
    use EnumsHelper;


    case  MALL = 'mall';
    case  PRESALE = 'presale';


    public static function labels() : array
    {
        return [
            self::MALL->value    => '标准',
            self::PRESALE->value => '预售',
        ];
    }


}
