<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RateStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShipStatusEnum;
use RedJasmine\Order\Enums\Orders\ShipTypeEnum;
use RedJasmine\Support\DataTransferObjects\Data;
use Spatie\LaravelData\Optional;

class OrderProductDTO extends Data
{
    /**
     * 商品类型 自定义
     * @var string
     */
    public string       $orderProductType;
    public ShipTypeEnum $shipType;
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
    public string|int|float|Optional $taxAmount      = 0;
    public string|int|float|Optional $paymentAmount  = 0;
    public string|int|float|Optional $refundAmount   = 0;
    public string|int|float|Optional $discountAmount = 0;
    public int                       $skuId          = 0;

    public string|Optional    $image;
    public ?int               $categoryId;
    public ?int               $sellerCategoryId;
    public ?string            $outerId;
    public ?string            $outerSkuId;
    public ?string            $barcode;
    public ?OrderStatusEnum   $orderStatus   = null;
    public ?ShipStatusEnum    $shipStatus    = null;
    public ?PaymentStatusEnum $paymentStatus = null;
    public ?RefundStatusEnum  $refundStatus  = null;
    public ?RateStatusEnum    $rateStatus    = null;


    public ?OrderProductInfoDTO $info;
}
