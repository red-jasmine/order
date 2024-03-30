<?php

namespace RedJasmine\Order\Services\Order\Data;


use Illuminate\Support\Carbon;
use RedJasmine\Support\Data\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

/**
 * 支付信息
 */
class OrderPaidInfoData extends Data
{

    /**
     * 支付金额
     * @var string
     */
    public string $paymentAmount;

    /**
     * 支付单类型
     * @var string|null
     */
    public ?string $paymentType;

    /**
     * 支付单ID
     * @var int|null
     */
    public ?int $paymentId;

    /**
     * 支付渠道
     * @var string|null
     */
    public ?string $paymentChannel;

    #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d H:i:s')]
    public ?Carbon $paymentTime;

}
