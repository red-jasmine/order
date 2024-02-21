<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Support\DataTransferObjects\Data;


class OrderInfoDTO extends Data
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
