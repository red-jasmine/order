<?php

namespace RedJasmine\Order\Domain\Exceptions;

use RedJasmine\Support\Exceptions\AbstractException;

class OrderException extends AbstractException
{


    public const ORDER_STATUS_NOT_ALLOW   = 210510;
    public const PAYMENT_STATUS_NOT_ALLOW = 210511;

    protected static array $codes = [

        self::ORDER_STATUS_NOT_ALLOW => '订单状态限制操作',

        self::PAYMENT_STATUS_NOT_ALLOW => '支付状态限制操作',
    ];

}
