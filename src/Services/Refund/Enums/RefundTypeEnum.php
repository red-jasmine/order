<?php

namespace RedJasmine\Order\Services\Refund\Enums;


use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 退款类型
 */
enum RefundTypeEnum: string
{
    use EnumsHelper;

    case REFUND_ONLY = 'refund_only';

    case  RETURN_GOODS_REFUND = 'return_goods_refund';

    case  EXCHANGE_GOODS = 'exchange_goods';

    case  SERVICE = 'service';


    public static function labels() : array
    {
        return [
            self::REFUND_ONLY->value         => '仅退款',
            self::RETURN_GOODS_REFUND->value => '退货退款',
            self::EXCHANGE_GOODS->value      => '换货',
            self::SERVICE->value             => '维修',
        ];

    }
}
