<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 付款状态
 */
enum PaymentStatusEnum: string
{

    use EnumsHelper;

    // 未支付
    case WAIT_PAY = 'WAIT_PAY';
    // 支付中
    case PAYING = 'PAYING';
    // 支付成功
    case PAID = 'PAID';
    // 支付失败
    case PAY_FAIL = 'PAY_FAIL';
    // 不支付
    case NO_PAYMENT = 'NO_PAYMENT';

    public static function names() : array
    {
        return [
            self::WAIT_PAY->value   => '待支付',
            self::PAYING->value     => '支付中',
            self::PAID->value       => '支付成功',
            self::PAY_FAIL->value   => '支付失败',
            self::NO_PAYMENT->value => '无需支付',
        ];

    }
}
