<?php

namespace RedJasmine\Order\Enums\Refund;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 退款阶段
 */
enum RefundPhaseEnum: string
{
    use EnumsHelper;

    case  ON_SALE = 'ON_SALE';
    case  AFTER_SALE = 'AFTER_SALE';


    public static function names() : array
    {
        return [
            self::ON_SALE->value    => '售中',
            self::AFTER_SALE->value => '售后',
        ];

    }
}
