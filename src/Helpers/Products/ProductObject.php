<?php

namespace RedJasmine\Order\Helpers\Products;
use Illuminate\Support\Str;
use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;

class ProductObject implements ProductInterface
{

    protected array $attributes = [];


    protected string            $productType;
    protected int               $productId;
    protected int               $skuId;
    protected string            $title;
    protected string            $price;
    protected string            $costPrice      = '0';
    protected int               $num;
    protected ?string           $image;
    protected ShippingTypeEnums $shippingType;
    protected string            $taxAmount      = '0';
    protected string            $discountAmount = '0';

    /**
     * @param array{shipping_type:string|ShippingTypeEnums,product_type:string,product_id:int,sku_id:int,title:string,price:string,num:int,image:string|null} $data
     */
    public function __construct(array $data = [])
    {

        foreach ($data as $key => $value) {
            $key = Str::camel($key);
            if (property_exists($this, $key)) {
                $method = 'set' . Str::studly($key);
                $this->$method($value);
            }
        }
    }

    public function getProductType() : string
    {
        return $this->productType;
    }

    public function setProductType(string $productType) : ProductObject
    {
        $this->productType = $productType;
        return $this;
    }

    public function getShippingType() : ShippingTypeEnums
    {
        return $this->shippingType;
    }

    public function setShippingType($shippingType) : ProductObject
    {
        $this->shippingType = ShippingTypeEnums::from($shippingType);
        return $this;
    }

    public function getProductId() : int
    {
        return $this->productId;
    }

    public function setProductId(int $productID) : ProductObject
    {
        $this->productId = $productID;
        return $this;
    }

    public function getSkuId() : int
    {
        return $this->skuId;
    }

    public function setSkuId(?int $skuID) : ProductObject
    {
        $this->skuId = $skuID;
        return $this;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle(string $title) : ProductObject
    {
        $this->title = $title;
        return $this;
    }

    public function getPrice() : string
    {
        return $this->price;
    }

    public function setPrice(string $price) : ProductObject
    {
        $this->price = $price;
        return $this;
    }

    public function getNum() : int
    {
        return $this->num;
    }

    public function setNum(int $num) : ProductObject
    {
        $this->num = $num;
        return $this;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }

    public function setImage(?string $image) : ProductObject
    {
        $this->image = $image;
        return $this;
    }

    public function getTaxAmount() : string
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(string $taxAmount) : void
    {
        $this->taxAmount = $taxAmount;
    }

    public function getDiscountAmount() : string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(string $discountAmount) : void
    {
        $this->discountAmount = $discountAmount;
    }

    public function getCostPrice() : string
    {
        return $this->costPrice;
    }

    public function setCostPrice(string $costPrice) : static
    {
        $this->costPrice = $costPrice;
        return $this;
    }


}
