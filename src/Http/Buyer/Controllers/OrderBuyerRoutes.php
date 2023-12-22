<?php

namespace RedJasmine\Order\Http\Buyer\Controllers;

use Illuminate\Support\Facades\Route;
use RedJasmine\Order\Http\Buyer\Controllers\Api\OrderController;


class OrderBuyerRoutes
{


    public static function api() : void
    {
        Route::group([
                         'prefix' => 'order'
                     ], function () {
            Route::apiResource('orders', OrderController::class)->names('order.buyer.order');
        });
    }

}
