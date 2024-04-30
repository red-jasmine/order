<?php

namespace RedJasmine\Order\Tests\Application;

use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\OrderQueryService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\Services\RefundQueryService;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\RefundRepository;
use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;
use RedJasmine\Order\Tests\Fixtures\Users\Seller;
use RedJasmine\Order\Tests\Fixtures\Users\User;
use RedJasmine\Order\Tests\TestCase;
use RedJasmine\Support\Contracts\UserInterface;

class ApplicationTest extends TestCase
{

    protected function buyer() : UserInterface
    {
        return User::make(1);
    }

    protected function seller() : UserInterface
    {
        return Seller::make(1);
    }

    public function fake() : OrderFake
    {
        return new OrderFake();
    }


    protected function orderCommandService() : OrderCommandService
    {
        return app(OrderCommandService::class)->setOperator($this->buyer());
    }

    protected function orderQueryService() : OrderQueryService
    {
        return app(OrderQueryService::class)->withQuery($this->buyer());
    }

    protected function refundCommandService() : RefundCommandService
    {
        return app(RefundCommandService::class)->setOperator($this->buyer());
    }

    protected function refundQueryService() : RefundQueryService
    {
        return app(RefundQueryService::class)->setQuery($this->buyer());
    }

    protected function orderRepository() : OrderRepositoryInterface
    {
        return app(OrderRepositoryInterface::class);
    }

    protected function refundRepository() : RefundRepository
    {
        return app(RefundRepository::class);
    }


}
