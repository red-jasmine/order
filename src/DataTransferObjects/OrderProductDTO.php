<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RateStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderProductDTO extends Data
{
    public ShippingTypeEnum          $shippingType;
    public string                    $productType;
    public int                       $productId;
    public int                       $num;
    public string|int|float          $price;
    public string|int|float|Optional $costPrice;
    public string|int|float|Optional $taxAmount      = 0;
    public string|int|float|Optional $paymentAmount  = 0;
    public string|int|float|Optional $refundAmount   = 0;
    public string|int|float|Optional $discountAmount = 0;
    public int                       $skuId          = 0;
    public string                    $title;
    public string|Optional           $image;
    public ?int                      $categoryId;
    public ?int                      $sellerCategoryId;
    public ?string                   $outerIid;
    public ?string                   $outerSkuId;
    public ?string                   $barcode;
    public ?OrderStatusEnum          $orderStatus    = null;
    public ?ShippingStatusEnum       $shippingStatus = null;
    public ?PaymentStatusEnum        $paymentStatus  = null;
    public ?RefundStatusEnum         $refundStatus   = null;
    public ?RateStatusEnum           $rateStatus     = null;


    public ?OrderProductInfoDTO $info;
}
