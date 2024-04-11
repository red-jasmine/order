<?php

namespace RedJasmine\Order\Application;

use Illuminate\Support\ServiceProvider;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\OrderRepository;


/**
 * 订单 应用层 服务提供者
 */
class OrderApplicationServiceProvider extends ServiceProvider
{
    public function register() : void
    {

        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );

    }

    public function boot() : void
    {
    }
}
