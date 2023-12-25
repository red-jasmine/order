<?php

namespace RedJasmine\Order\Services\Orders\Pipelines\Products;

use RedJasmine\Order\Models\OrderProduct;

class ProductCategoryApplying
{
    public function handle(OrderProduct $orderProduct, \Closure $next)
    {
        $orderProduct->category_id = 22;
        return $next($orderProduct);
    }

}
