<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 订单状态
 */
enum OrderStatusEnums: string
{
    use EnumsHelper;

    case  WAIT_BUYER_PAY = 'WAIT_BUYER_PAY'; // 待付款
    case  PAID_FORBID_CONSIGN = 'PAID_FORBID_CONSIGN'; // 已付款管控发货
    case  WAIT_SELLER_SEND_GOODS = 'WAIT_SELLER_SEND_GOODS'; // 待发货
    case  WAIT_BUYER_CONFIRM_GOODS = 'WAIT_BUYER_CONFIRM_GOODS'; // 待收货
    case  TRADE_FINISHED = 'TRADE_FINISHED'; // 交易成功
    case  TRADE_CANCEL = 'TRADE_CANCEL'; //已取消 未支付
    case  TRADE_CLOSED = 'TRADE_CLOSED'; // 已关闭 已支付已退款


    public static function names() : array
    {
        return [
            self::WAIT_BUYER_PAY->value           => '待付款',
            self::PAID_FORBID_CONSIGN->value      => '待确认',
            self::WAIT_SELLER_SEND_GOODS->value   => '待发货',
            self::WAIT_BUYER_CONFIRM_GOODS->value => '待收货',
            self::TRADE_FINISHED->value           => '已完成',
            self::TRADE_CANCEL->value             => '已取消',
            self::TRADE_CLOSED->value             => '已关闭',
        ];
    }
}
