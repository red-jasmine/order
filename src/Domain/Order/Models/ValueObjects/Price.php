<?php

namespace RedJasmine\Order\Domain\Order\Models\ValueObjects;

class Price
{

    private string $price;

    /**
     */
    public function __construct(string|int|float $price)
    {

        // TODO 数据验证
        // throw new   InvalidArgumentException('金额不能小于0');
        $this->price = bcadd($price, 0, 2);
    }

    public function getPrice() : string
    {
        return bcadd($this->price, 0, 2);
    }


}
