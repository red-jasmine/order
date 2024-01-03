<?php

namespace RedJasmine\Order\Enums\Refund;


use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 退款类型
 */
enum RefundTypeEnum: string
{
    use EnumsHelper;

    case REFUND_ONLY = 'REFUND_ONLY';

    case  RETURN_GOODS_REFUND = 'RETURN_GOODS_REFUND';

    case  EXCHANGE_GOODS = 'EXCHANGE_GOODS';


    public static function names() : array
    {
        return [
            self::REFUND_ONLY->value         => '仅退款',
            self::RETURN_GOODS_REFUND->value => '退货退款',
            self::EXCHANGE_GOODS->value      => '换货',
        ];

    }
}
