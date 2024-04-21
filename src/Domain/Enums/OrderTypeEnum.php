<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单类型
 */
enum OrderTypeEnum: string
{
    use EnumsHelper;

    case  SOP = 'sop';
    case  PRESALE = 'presale';


    public static function labels() : array
    {
        return [
            self::SOP->value     => '标准',
            self::PRESALE->value => '预售',
        ];
    }


}
