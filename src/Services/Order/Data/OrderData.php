<?php

namespace RedJasmine\Order\Services\Order\Data;

use Illuminate\Support\Collection;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\OrderTypeEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingTypeEnum;
use RedJasmine\Support\Data\Data;
use RedJasmine\Support\Data\UserData;

class OrderData extends Data
{
    public static function morphs() : array
    {
        return [ 'seller', 'buyer', 'channel', 'store', 'guide' ];
    }

    public string           $title;
    public UserData         $seller;
    public UserData         $buyer;
    public ShippingTypeEnum $shippingType;
    public OrderTypeEnum    $orderType;
    public ?string          $source             = null;
    public ?string          $outerOrderId       = null;
    public ?string          $sellerCustomStatus = null;
    public ?UserData        $channel            = null;
    public ?UserData        $store              = null;
    public ?UserData        $guide              = null;
    public string|float|int $freightAmount      = 0;
    public string|float|int $discountAmount     = 0;


    // 虚拟商品 通知方
    public ?string $contact  = null;
    public ?string $password = null;

    public ?string $clientType;
    public ?string $clientIp;

    /** @var Collection<OrderProductData> */
    public Collection $products;

    public ?OrderAddressData $address;


    public ?OrderInfoData $info;


}
