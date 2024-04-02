<?php

namespace RedJasmine\Order\Domain\Order\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货状态
 */
enum ShippingStatusEnum: string
{

    use EnumsHelper;

    case WAIT_SEND = 'wait_send'; // 等待发货
    case PART_SHIPPED = 'part_shipped'; // 部分发货
    case SHIPPED = 'shipped'; // 全部已发货


    public static function labels() : array
    {
        return [

            self::WAIT_SEND->value    => '待发货',
            self::PART_SHIPPED->value => '部分发货',
            self::SHIPPED->value      => '已发货',
        ];
    }
}
