<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Support\DataTransferObjects\UserData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class OrderData extends Data
{

    public UserData $seller;

    public ?UserData $buyer = null;

    public ShippingTypeEnum $shippingType;


}
