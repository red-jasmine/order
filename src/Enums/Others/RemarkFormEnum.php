<?php

namespace RedJasmine\Order\Enums\Others;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum RemarkFormEnum: string
{

    use EnumsHelper;

    case SELLER = 'seller';
    case BUYER = 'buyer';


    public static function labels() : array
    {
        return [
            self::SELLER->value => '卖家',
            self::BUYER->value  => '买家',
        ];
    }

}
