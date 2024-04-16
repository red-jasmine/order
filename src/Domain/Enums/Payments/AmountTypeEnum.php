<?php

namespace RedJasmine\Order\Domain\Enums\Payments;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum AmountTypeEnum: string
{

    use EnumsHelper;

    case FULL = 'full';
    case PREPAY = 'prepay';
    case BALANCE = 'balance';
    case REFUND = 'refund';


    public static function lables() : array
    {

        return [
            self::FULL->value    => '全款',
            self::PREPAY->value  => '预付款',
            self::BALANCE->value => '尾款',
            self::REFUND->value  => '退款',
        ];
    }


}