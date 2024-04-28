<?php

namespace RedJasmine\Order\Domain\Exceptions;

use RedJasmine\Support\Exceptions\AbstractException;

class RefundException extends AbstractException
{

    public const  REFUND_STATUS_NOT_ALLOW = 211510;
    public const  REFUND_TYPE_NOT_ALLOW   = 211520;

    protected static array $codes = [
        self::REFUND_STATUS_NOT_ALLOW => '退款不支持操作',
        self::REFUND_TYPE_NOT_ALLOW   => '退款类型不支持操作',
    ];


}
