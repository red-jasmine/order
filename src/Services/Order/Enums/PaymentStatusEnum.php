<?php

namespace RedJasmine\Order\Services\Order\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 付款状态
 */
enum PaymentStatusEnum: string
{

    use EnumsHelper;

    // 未支付
    case WAIT_PAY = 'wait_pay';
    // 支付中
    case PAYING = 'paying';
    // 部分支付
    case PART_PAY = 'part_pay';
    // 支付成功
    case PAID = 'paid';
    // 支付失败
    case PAY_FAIL = 'pay_fail';
    // 无需支付
    case NO_PAYMENT = 'no_payment';

    public static function labels() : array
    {
        return [
            self::WAIT_PAY->value   => '待支付',
            self::PAYING->value     => '支付中',
            self::PART_PAY->value   => '部分支付',
            self::PAID->value       => '支付成功',
            self::PAY_FAIL->value   => '支付失败',
            self::NO_PAYMENT->value => '无需支付',
        ];

    }
}
