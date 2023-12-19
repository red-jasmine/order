<?php

namespace RedJasmine\Order\Contracts;

use RedJasmine\Order\Enums\Orders\Types\ShippingTypeEnums;

interface ProductInterface
{

    /**
     * 发货类型
     * @return ShippingTypeEnums
     */
    public function getShippingType() : ShippingTypeEnums;

    /**
     *
     * @return string
     */
    public function getProductType() : string;

    /**
     * 商品ID
     * @return int
     */
    public function getProductID() : int;

    /**
     * 价格
     * @return string
     */
    public function getPrice() : string;

    /**
     * 数量
     * @return int
     */
    public function getNum() : int;

    /**
     * 规格ID
     * @return int
     */
    public function getSkuID() : int;


    public function getTitle() : string;


    public function getImage() : ?string;


}
