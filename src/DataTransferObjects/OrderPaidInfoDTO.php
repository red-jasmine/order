<?php

namespace RedJasmine\Order\DataTransferObjects;


use Illuminate\Support\Carbon;
use RedJasmine\Support\DataTransferObjects\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;


class OrderPaidInfoDTO extends Data
{
    /**
     * 支付类型
     * @var string|null
     */
    public ?string $paymentType;

    public ?int $paymentId;

    public ?string $paymentChannel;

    #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d H:i:s')]
    public ?Carbon $paymentTime;

}
