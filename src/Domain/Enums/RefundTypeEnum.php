<?php

namespace RedJasmine\Order\Domain\Enums;


use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 退款类型
 */
enum RefundTypeEnum: string
{
    use EnumsHelper;

    case REFUND_ONLY = 'refund';

    case  RETURN_GOODS_REFUND = 'return_goods_refund';

    case  EXCHANGE = 'exchange';

    case  SERVICE = 'service';

    case  OTHER = 'other';


    public static function labels() : array
    {
        return [
            self::REFUND_ONLY->value         => '仅退款',
            self::RETURN_GOODS_REFUND->value => '退货退款',
            self::EXCHANGE->value            => '换货',
            self::SERVICE->value             => '维修',
            self::OTHER->value               => '其他',
        ];

    }
}
