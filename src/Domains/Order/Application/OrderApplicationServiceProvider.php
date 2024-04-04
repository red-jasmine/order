<?php

namespace RedJasmine\Order\Domains\Order\Application;

use Illuminate\Support\ServiceProvider;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domains\Order\Infrastructure\Repositories\Eloquent\OrderRepository;


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
