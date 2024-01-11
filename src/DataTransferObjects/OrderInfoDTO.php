<?php

namespace RedJasmine\Order\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderInfoDTO extends Data
{
    public ?string $sellerRemarks;
    public ?string $sellerMessage;
    public ?string $buyerRemarks;
    public ?string $buyerMessage;
    public ?array  $sellerExtends;
    public ?array  $otherExtends;

}
