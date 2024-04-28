<?php

namespace RedJasmine\Order\Domain\Exceptions;

use RedJasmine\Support\Exceptions\AbstractException;

class OrderException extends AbstractException
{
    // 状态类限制错误
    public const ORDER_STATUS_NOT_ALLOW    = 210510;
    public const PAYMENT_STATUS_NOT_ALLOW  = 210511;
    public const SHIPPING_STATUS_NOT_ALLOW = 210512;

    protected static array $codes = [
        self::ORDER_STATUS_NOT_ALLOW    => '订单状态限制操作',
        self::PAYMENT_STATUS_NOT_ALLOW  => '支付状态限制操作',
        self::SHIPPING_STATUS_NOT_ALLOW => '发货状态限制操作',
    ];

}
