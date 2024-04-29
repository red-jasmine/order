<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Data;


use RedJasmine\Order\Domain\Enums\OrderProductTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\ValueObjects\PromiseServices;
use RedJasmine\Support\Data\Data;
use RedJasmine\Support\Domain\Models\Casts\AmountCastTransformer;
use RedJasmine\Support\Domain\Models\ValueObjects\Amount;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

class OrderProductData extends Data
{
    public function __construct()
    {
        $this->taxAmount      = new Amount(0);
        $this->discountAmount = new Amount(0);
        $this->costPrice      = new Amount(0);

    }


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
    public string $productType;
    public int    $productId;
    public int    $skuId = 0;
    public int    $num;

    public Amount $price;
    public Amount $costPrice;
    public Amount $taxAmount;
    public Amount $discountAmount;

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
     * @var PromiseServices|null
     */
    public ?PromiseServices $promiseServices;

    public ?OrderProductInfoData $info;
}
