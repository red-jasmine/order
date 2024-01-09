<?php

namespace RedJasmine\Order\Services\Orders\Pipelines;

use Closure;
use Illuminate\Support\Facades\Validator;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\Validators\OrderValidate;

class OrderValidatePipeline
{

    public function handle(Order $order, Closure $next)
    {
        $orderValidate = new OrderValidate();

        $validator     = Validator::make($order->toArray(), $orderValidate->rules());
        $validator->validate();

        return $next($order);
    }
}
