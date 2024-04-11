<?php

namespace RedJasmine\Order\Domain\Models\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class Money implements Cast, Transformer, CastsAttributes
{


    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return $value;
    }


    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context) : mixed
    {

        return $value;
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context) : mixed
    {
        return $value;
    }


}
