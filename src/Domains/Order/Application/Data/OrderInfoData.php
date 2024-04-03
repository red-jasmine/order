<?php

namespace RedJasmine\Order\Application\Order\Data;

namespace RedJasmine\Order\Domains\Order\Application\Data;


class OrderInfoData extends Data
{
    public ?string $sellerRemarks;
    public ?string $sellerMessage;
    public ?string $buyerRemarks;
    public ?string $buyerMessage;
    public ?array  $sellerExtends;
    public ?array  $buyerExtends;
    public ?array  $otherExtends;
    public ?array  $tools;

}
