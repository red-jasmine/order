<?php

namespace RedJasmine\Order\Domain\Models\ValueObjects;

use InvalidArgumentException;
use RedJasmine\Support\Domain\Models\ValueObjects\ValueObject;


class PromiseServiceValue extends ValueObject
{

    public const  UNSUPPORTED = 'unsupported';

    protected array $enums = [
        self::UNSUPPORTED => '不支持',
    ];

    private string $value;

    public function __construct(string $value = self::UNSUPPORTED)
    {

        $this->isValid($value);
        $this->value = $value;
    }


    protected function allowTimeSuffix() : array
    {
        return [
            'minute',
            'hour',
            'day',
            'month',
            'year'
        ];

    }

    protected function isValid(string $value) : bool
    {

        if (in_array($value, array_keys($this->enums), true)) {
            return true;
        }
        $allowTimeSuffixString = implode('|', $this->allowTimeSuffix());
        $pattern               = "/^[1-9]+[\d]*($allowTimeSuffixString)$/";

        if ((bool)preg_match($pattern, $value) === false) {
            throw new InvalidArgumentException('参数值错误:'.$value);
        }
        return true;
    }

    public function value() : string
    {
        return $this->value;
    }
}
