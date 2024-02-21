<?php

namespace RedJasmine\Order\DataTransferObjects;

use Dflydev\DotAccessData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderRemarksDTO extends Data
{
    public string $type;
    public string $text;

}
