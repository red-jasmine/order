<?php

namespace RedJasmine\Order\UI\Http\Buyer\Api;

use Illuminate\Support\Facades\Route;
use RedJasmine\Order\UI\Http\Buyer\Api\Controller\OrderController;


class OrderBuyerApiRoute
{

    public static function route() : void
    {
        Route::group([
                         'prefix' => 'order'
                     ], function () {
            Route::apiResource('orders', OrderController::class)->names('order.buyer.orders');
        });
    }
}
