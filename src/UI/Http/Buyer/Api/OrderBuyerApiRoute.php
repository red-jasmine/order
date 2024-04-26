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

            // 订单
            Route::apiResource('orders', OrderController::class)->names('order.buyer.orders');
            Route::post('orders/cancel', [ OrderController::class, 'cancel' ])->name('order.buyer.orders.cancel');
            Route::post('orders/paying', [ OrderController::class, 'paying' ])->name('order.buyer.orders.paying');

        });
    }
}
