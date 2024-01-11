<?php

namespace RedJasmine\Order\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OrderProductDTO extends Data
{
    public string                    $productType;
    public int                       $productId;
    public int                       $num;
    public string|int|float          $price;
    public string|int|float|Optional $costPrice;
    public string|int|float|Optional $discountAmount;
    public int                       $skuId = 0;
    public string                    $title;
    public string|Optional           $image;
    public ?int                      $categoryId;
    public ?int                      $sellerCategoryId;
    public ?string                   $outerIid;
    public ?string                   $outerSkuId;
    public ?string                   $barcode;

    public ?string $sellerRemarks;
    public ?string $sellerMessage;
    public ?string $buyerRemarks;
    public ?string $buyerMessage;
    public ?array  $sellerExtends;
    public ?array  $otherExtends;
    public ?array  $tools;
}
