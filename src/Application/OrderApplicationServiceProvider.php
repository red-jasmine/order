<?php

namespace RedJasmine\Order\Application;

use Illuminate\Support\ServiceProvider;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Domain\Repositories\RefundRepositoryInterface;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\OrderRepository;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\RefundRepository;


/**
 * 订单 应用层 服务提供者
 */
class OrderApplicationServiceProvider extends ServiceProvider
{
    public function register() : void
    {

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        $this->app->bind(RefundRepositoryInterface::class, RefundRepository::class);
    }

    public function boot() : void
    {
    }
}