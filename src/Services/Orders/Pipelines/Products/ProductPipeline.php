<?php

namespace RedJasmine\Order\Services\Orders\Pipelines\Products;

use Closure;
use RedJasmine\Order\Models\OrderProduct;

class ProductPipeline
{

    public function handle(OrderProduct $orderProduct, Closure $next)
    {
        $orderProduct->title .= '【测试】';

        return $next($orderProduct);
    }

}
