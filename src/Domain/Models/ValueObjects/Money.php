<?php

namespace RedJasmine\Order\Domain\Models\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class Money implements CastsAttributes
{
    public function __construct(public readonly mixed $money = 0)
    {
    }


    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement get() method.
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement set() method.
    }


}
