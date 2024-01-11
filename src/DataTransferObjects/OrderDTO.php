<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\OrderTypeEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Support\DataTransferObjects\UserData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderDTO extends Data
{

    public ?array $extends = [];

    public string              $title;
    public UserData            $seller;
    public ?UserData           $buyer    = null;
    public ShippingTypeEnum    $shippingType;
    public OrderTypeEnum       $orderType;
    public OrderStatusEnum     $orderStatus;
    public ?ShippingStatusEnum $shippingStatus;
    public ?PaymentStatusEnum  $paymentStatus;
    public ?RefundStatusEnum   $refundStatus;
    public ?string             $source   = null;
    public ?string             $email    = null;
    public ?string             $password = null;
    public ?UserData           $channel  = null;
    public ?UserData           $store    = null;
    public ?UserData           $guide    = null;

    public string|float|int $freightAmount  = 0;
    public string|float|int $discountAmount = 0;

    /** @var DataCollection<OrderProductDTO> */
    public DataCollection $products;


    public ?string $sellerRemarks;
    public ?string $sellerMessage;
    public ?string $buyerRemarks;
    public ?string $buyerMessage;
    public ?array  $sellerExtends;
    public ?array  $otherExtends;

}
