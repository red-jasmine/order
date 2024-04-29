<?php

namespace RedJasmine\Order\Domain\Models\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MoneyCastAndTransformer implements Cast, Transformer
{

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context) : mixed
    {
        return new Money($value);
    }

    /**
     * @param DataProperty          $property
     * @param Money                 $value
     * @param TransformationContext $context
     *
     * @return mixed
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context) : mixed
    {
        return $value->money;
    }


}
