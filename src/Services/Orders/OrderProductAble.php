<?php

namespace RedJasmine\Order\Services\Orders;

use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;

trait OrderProductAble
{

    public static function make(ProductInterface $product) : static
    {
        $model                  = new static();
        $model->shipping_type   = $product->getShippingType();
        $model->product_id      = $product->getProductID();
        $model->product_type    = $product->getProductType();
        $model->sku_id          = $product->getSkuID();
        $model->price           = $product->getPrice();
        $model->cost_price      = $product->getCostPrice();
        $model->title           = $product->getTitle();
        $model->image           = $product->getImage();
        $model->num             = $product->getNum();
        $model->tax_amount      = $product->getTaxAmount();
        $model->discount_amount = $product->getDiscountAmount();
        return $model;
    }

    public function getShippingType() : ShippingTypeEnums
    {
        return $this->shipping_type;
    }

    public function getProductType() : string
    {
        return $this->product_type;
    }

    public function getProductId() : int
    {
        return $this->product_id;
    }

    public function getPrice() : string
    {
        return $this->price;
    }

    public function getCostPrice() : string
    {
        return $this->cost_price;
    }

    public function getTaxAmount() : string
    {
        return $this->tax_amount;
    }

    public function getDiscountAmount() : string
    {
        return $this->discount_amount;
    }

    public function getNum() : int
    {
        return $this->num;
    }

    public function getSkuId() : int
    {
        return $this->sku_id;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }


}
