<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\OrderTypeEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RateStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use RedJasmine\Support\DataTransferObjects\Data;
use RedJasmine\Support\DataTransferObjects\UserDTO;
use Spatie\LaravelData\DataCollection;

class OrderDTO extends Data
{

    public ?array $parameters = [];

    public string             $title;
    public UserDTO            $seller;
    public UserDTO            $buyer;
    public ShippingTypeEnum       $shippingType;
    public OrderTypeEnum      $orderType;
    public OrderStatusEnum    $orderStatus;
    public ?ShippingStatusEnum    $shippingStatus    = null;
    public ?PaymentStatusEnum $paymentStatus = null;
    public ?RefundStatusEnum  $refundStatus  = null;
    public ?RateStatusEnum    $rateStatus    = null;
    public ?string            $source        = null;

    public ?UserDTO         $channel        = null;
    public ?UserDTO         $store          = null;
    public ?UserDTO         $guide          = null;
    public string|float|int $freightAmount  = 0;
    public string|float|int $discountAmount = 0;


    // 虚拟商品 通知方
    public ?string $notifiable = null;
    public ?string $password   = null;

    public ?string $clientType;
    public ?string $clientIp;

    /** @var DataCollection<OrderProductDTO> */
    public DataCollection $products;

    public ?OrderAddressDTO $address;


    public ?OrderInfoDTO $info;


}
