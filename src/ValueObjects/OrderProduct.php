<?php

namespace RedJasmine\Order\ValueObjects;

use Illuminate\Support\Str;

class OrderProduct
{

    public string $title;
    public string $price;
    public int    $num;
    public string $productType = 'product';
    public int    $productId   = 0;
    public int    $skuId       = 0;


    public function __construct(protected $attributes = [])
    {
        $this->fill($this->attributes);
    }

    public function fill(array $attributes = []) : OrderProduct
    {
        foreach ($attributes as $key => $value) {
            $key = Str::camel($key);
            if (property_exists($this, $key)) {
                $method = 'set' . Str::studly($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                } else {
                    $this->$key = $value;
                }

            }
        }
        return $this;
    }

    public static function make($attributes = []) : static
    {
        return new static($attributes);
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
    }


}
