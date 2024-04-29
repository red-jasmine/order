<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Data;


use RedJasmine\Order\Domain\Enums\OrderProductTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\ValueObjects\Money;
use RedJasmine\Order\Domain\Models\ValueObjects\MoneyCastAndTransformer;
use RedJasmine\Support\Data\Data;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

class OrderProductData extends Data
{

    /**
     * 商品类型
     * @var OrderProductTypeEnum
     */
    public OrderProductTypeEnum $orderProductType;
    /**
     * 发货类型
     * @var ShippingTypeEnum
     */
    public ShippingTypeEnum $shippingType;


    public string  $title;
    public ?string $skuName;
    /**
     * 商品多态类型
     * @var string
     */
    public string           $productType;
    public int              $productId;
    public int              $skuId          = 0;
    public int              $num;
    /**
     * @var Money
     */
    #[WithCastAndTransformer(MoneyCastAndTransformer::class)]
    public Money $price;
    public string|int|float $costPrice      = 0;
    public string|int|float $taxAmount      = 0;
    public string|int|float $paymentAmount  = 0;
    public string|int|float $discountAmount = 0;

    public int     $categoryId          = 0;
    public int     $sellerCategoryId    = 0;
    public ?string $image;
    public ?string $outerId;
    public ?string $outerSkuId;
    public ?string $barcode;
    public ?string $sellerCustomStatus  = null;
    public ?string $outerOrderProductId = null;

    /**
     * 赠送积分
     * @var int
     */
    public int $giftPoint = 0;


    /**
     * 承诺服务
     * @var PromiseServices|null
     */
    public ?PromiseServices $promiseServices;



    // 支持的售后服务时效 TODO
    // - 退款；不支持、发货前可退 、7hour,7day  基于签收时间
    // - 换货: 0,15day 基于签收时间
    // - 保修: 0, 7hour, 7day,3month,1yeas 基于签收时间
    // - 保价: 0,15day,1month   基于下单时间

    public ?OrderProductInfoData $info;
}
