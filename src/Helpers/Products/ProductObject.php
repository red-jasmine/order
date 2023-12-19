<?php

namespace RedJasmine\Order\Helpers\Products;


use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\Types\ShippingTypeEnums;

class ProductObject implements ProductInterface
{


    protected string            $productType;
    protected int               $productID;
    protected int               $skuID;
    protected string            $title;
    protected string            $price;
    protected int               $num;
    protected ?string           $image;
    protected ShippingTypeEnums $shippingType;

    /**
     * @param array{shipping_type:string|ShippingTypeEnums,product_type:string,product_id:int,sku_id:int,title:string,price:string,num:int,image:string|null} $data
     */
    public function __construct(array $data = [])
    {
        isset($data['shipping_type']) ? $this->shippingType = ShippingTypeEnums::tryFrom($data['shipping_type']) : null;
        isset($data['product_type']) ? $this->productType = $data['product_type'] : null;
        isset($data['product_id']) ? $this->productID = $data['product_id'] : null;
        isset($data['sku_id']) ? $this->skuID = $data['sku_id'] : null;
        isset($data['title']) ? $this->title = $data['title'] : null;
        isset($data['price']) ? $this->price = $data['price'] : null;
        isset($data['num']) ? $this->num = $data['num'] : null;
        isset($data['image']) ? $this->image = $data['image'] : null;
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

    public function setShippingType(ShippingTypeEnums $shippingType) : ProductObject
    {
        $this->shippingType = $shippingType;
        return $this;
    }

    public function getProductID() : int
    {
        return $this->productID;
    }

    public function setProductID(int $productID) : ProductObject
    {
        $this->productID = $productID;
        return $this;
    }

    public function getSkuID() : int
    {
        return $this->skuID;
    }

    public function setSkuID(?int $skuID) : ProductObject
    {
        $this->skuID = $skuID;
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


}
