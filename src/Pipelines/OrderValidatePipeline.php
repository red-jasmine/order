<?php

namespace RedJasmine\Order\Pipelines;

use Closure;
use Illuminate\Support\Facades\Validator;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Validators\OrderValidate;

/**
 * 入库验证
 */
class OrderValidatePipeline
{

    public function handle(Order $order, Closure $next)
    {
        $orderValidate = new OrderValidate();

        $validator = Validator::make($order->toArray(), $orderValidate->rules());
        $validator->validate();
        return $next($order);
    }
}
