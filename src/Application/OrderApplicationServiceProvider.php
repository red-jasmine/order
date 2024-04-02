<?php

namespace RedJasmine\Order\Application;

use Illuminate\Support\ServiceProvider;


/**
 * 订单 应用层 服务提供者
 */
class OrderApplicationServiceProvider extends ServiceProvider
{
    public function register() : void
    {

        $this->app->bind(
            \RedJasmine\Order\Domain\Order\OrderRepositoryInterface::class,
            \RedJasmine\Order\Application\Order\Repositories\Eloquent\OrderRepository::class
        );

    }

    public function boot() : void
    {
    }
}
