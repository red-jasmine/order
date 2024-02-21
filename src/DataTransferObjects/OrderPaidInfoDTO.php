<?php

namespace RedJasmine\Order\DataTransferObjects;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderPaidInfoDTO extends Data
{
    /**
     * 支付类型
     * @var string|null
     */
    public ?string $paymentType;

    public ?int $paymentId;

    public ?string $paymentChannel;


    public ?Carbon $paymentTime;

}
