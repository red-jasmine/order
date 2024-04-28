<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Data;


use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Support\Data\Data;
use Spatie\LaravelData\Optional;

class OrderProductData extends Data
{
    /**
     * 商品类型 自定义
     * @var string
     */
    public string $orderProductType;
    /**
     * 发货类型
     * @var ShippingTypeEnum
     */
    public ShippingTypeEnum $shippingType;
    /**
     * 商品多态类型
     * @var string
     */
    public string           $productType;
    public int              $productId;
    public string           $title;
    public ?string          $skuName;
    public int              $num;
    public string|int|float $price;
    public string|int|float $costPrice           = 0;
    public string|int|float $taxAmount           = 0;
    public string|int|float $paymentAmount       = 0;
    public string|int|float $discountAmount      = 0;
    public int              $skuId               = 0;
    public int              $categoryId          = 0;
    public int              $sellerCategoryId    = 0;
    public ?string          $image;
    public ?string          $outerId;
    public ?string          $outerSkuId;
    public ?string          $barcode;
    public ?string          $sellerCustomStatus  = null;
    public ?string          $outerOrderProductId = null;

    // 支持的售后服务失效 TODO
    // - 退款； 不支持、7day
    // - 换货: 0,15day
    // - 保修: 1yeas
    // - 保价: 15day

    public ?OrderProductInfoData $info;
}
