<?php

namespace RedJasmine\Order\Domain\Order\Models\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RedJasmine\Support\Exceptions\InvalidArgumentException;
use function PHPUnit\Framework\assertInstanceOf;

class PriceCasts implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes) : Price
    {
        return new Price($model);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {

        if (!$value instanceof Price) {
            throw new InvalidArgumentException('金额设置错误');
        }

        return $value->getPrice();
    }


}
