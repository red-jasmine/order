<?php

namespace RedJasmine\Order\Contracts;

use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;

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
    public function getProductId() : int;

    /**
     * 价格
     * @return string
     */
    public function getPrice() : string;


    /**
     * 成本价格
     * @return string
     */
    public function getCostPrice() : string;

    /**
     * 税费
     * @return string
     */
    public function getTaxAmount() : string;


    /**
     * 优惠金额
     * @return string
     */
    public function getDiscountAmount() : string;

    /**
     * 数量
     * @return int
     */
    public function getNum() : int;

    /**
     * 规格ID
     * @return int
     */
    public function getSkuId() : int;


    /**
     * 标题
     * @return string
     */
    public function getTitle() : string;


    /**
     * 图片
     * @return string|null
     */
    public function getImage() : ?string;


}
