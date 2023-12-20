<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 发货状态
 */
enum ShippingStatusEnums: string
{

    use EnumsHelper;

    case WAIT_SEND = 'WAIT_SEND'; // 等待发货
    case PART_SHIPPED = 'PART_SHIPPED'; // 部分发货
    case SHIPPED = 'SHIPPED'; // 全部已发货


    public static function names() : array
    {
        return [

            self::WAIT_SEND->value    => '待发货',
            self::PART_SHIPPED->value => '部分发货',
            self::SHIPPED->value      => '已发货',
        ];
    }
}
