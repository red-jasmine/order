<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Data;

use RedJasmine\Order\Domain\Models\ValueObjects\PromiseServiceSupported;
use RedJasmine\Order\Domain\Models\ValueObjects\PromiseServiceSupportedCastAndTransformer;
use RedJasmine\Support\Data\Data;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

class PromiseServices extends Data
{

    #[WithCastAndTransformer(PromiseServiceSupportedCastAndTransformer::class)]
    public PromiseServiceSupported $refund;

    #[WithCastAndTransformer(PromiseServiceSupportedCastAndTransformer::class)]
    public PromiseServiceSupported $exchange;

    #[WithCastAndTransformer(PromiseServiceSupportedCastAndTransformer::class)]
    public PromiseServiceSupported $service;

    #[WithCastAndTransformer(PromiseServiceSupportedCastAndTransformer::class)]
    public PromiseServiceSupported $priceGuarantee;

    public function __construct()
    {
        $this->refund         = new PromiseServiceSupported();
        $this->exchange       = new PromiseServiceSupported();
        $this->service        = new PromiseServiceSupported();
        $this->priceGuarantee = new PromiseServiceSupported();
    }


}
