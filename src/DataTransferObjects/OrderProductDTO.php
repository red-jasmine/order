<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RateStatusEnum;
use RedJasmine\Order\Services\Order\Enums\RefundStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingTypeEnum;
use RedJasmine\Support\Data\Data;
use Spatie\LaravelData\Optional;

class OrderProductDTO extends Data
{
    /**
     * 商品类型 自定义
     * @var string
     */
    public string           $orderProductType;
    public ShippingTypeEnum $shippingType;
    /**
     * 商品多态类型
     * @var string
     */
    public string                    $productType;
    public int                       $productId;
    public string                    $title;
    public ?string                   $skuName;
    public int                       $num;
    public string|int|float          $price;
    public string|int|float|Optional $costPrice;
    public string|int|float|Optional $taxAmount           = 0;
    public string|int|float|Optional $paymentAmount       = 0;
    public string|int|float|Optional $refundAmount        = 0;
    public string|int|float|Optional $discountAmount      = 0;
    public int                       $skuId               = 0;
    public string|Optional           $image;
    public ?int                      $categoryId;
    public ?int                      $sellerCategoryId;
    public ?string                   $outerId;
    public ?string                   $outerSkuId;
    public ?string                   $barcode;
    public ?OrderStatusEnum          $orderStatus         = null;
    public ?ShippingStatusEnum       $shippingStatus      = null;
    public ?PaymentStatusEnum        $paymentStatus       = null;
    public ?RefundStatusEnum         $refundStatus        = null;
    public ?RateStatusEnum           $rateStatus          = null;
    public ?string                   $sellerCustomStatus  = null;
    public ?string                   $outerOrderProductId = null;
    public ?OrderProductInfoDTO      $info;
}
