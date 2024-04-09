<?php

namespace RedJasmine\Order\Domains\Order\Application\UserCases\Commands;

use DateTime;
use RedJasmine\Support\Data\Data;

class OrderPaidCommand extends Data
{
    public function __construct(
        public int      $id,
        public int      $orderPaymentId,
        public string   $amount,
        public string   $paymentType,
        public int      $paymentId,
        public DateTime $paymentTime,
        public ?string  $paymentChannel = null,
        /**
         * 支付渠道单号
         * @var string|null
         */
        public ?string  $paymentChannelNo = null,
        public ?string  $paymentMethod = null,
    )
    {
    }


}