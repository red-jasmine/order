<?php

namespace RedJasmine\Order\Tests\Application;

use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\OrderQueryService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\Services\RefundQueryService;
use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\RefundRepository;
use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;
use RedJasmine\Order\Tests\Fixtures\Users\Seller;
use RedJasmine\Order\Tests\Fixtures\Users\User;
use RedJasmine\Order\Tests\TestCase;
use RedJasmine\Support\Contracts\UserInterface;

class ApplicationTestCase extends TestCase
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
        return app(OrderQueryService::class)->withQuery(function ($query) {
            $query->onlyBuyer($this->buyer());
        });
    }

    protected function refundCommandService() : RefundCommandService
    {
        return app(RefundCommandService::class)->setOperator($this->buyer());
    }

    protected function refundQueryService() : RefundQueryService
    {
        return app(RefundQueryService::class)->withQuery(function ($query) {
            $query->onlyBuyer($this->buyer());
        });
    }

    protected function orderRepository() : OrderRepositoryInterface
    {
        return app(OrderRepositoryInterface::class);
    }

    protected function refundRepository() : RefundRepository
    {
        return app(RefundRepository::class);
    }


    /**
     * 准备数据 订单已支付
     * @return Order
     */
    protected function orderPaid() : Order
    {
        // 1、创建订单
        $fake               = $this->fake();
        $orderCreateCommand = OrderCreateCommand::from($fake->order());
        $order              = $this->orderCommandService()->create($orderCreateCommand);
        $this->assertInstanceOf(Order::class, $order);


        // 2、调用支付中
        $orderPayingCommand = OrderPayingCommand::from([ 'id' => $order->id, 'amount' => $order->payable_amount ]);
        $orderPayment       = $this->orderCommandService()->paying($orderPayingCommand);
        $this->assertInstanceOf(OrderPayment::class, $orderPayment);


        // 3、 设置支付成功
        $orderPaidCommand = $fake->paid([
                                            'id'               => $order->id,
                                            'order_payment_id' => $orderPayment->id,
                                            'amount'           => $orderPayment->payment_amount,
                                        ]);

        $this->orderCommandService()->paid($orderPaidCommand);
        return $order;
    }


    /**
     * 订单已发货
     * @return Order
     */
    public function orderPaidAndShipping() : Order
    {
        $order = $this->orderPaid();

        $orderShippingLogisticsCommand = $this->fake()->shippingLogistics([ 'id' => $order->id ]);

        $this->orderCommandService()->shippingLogistics($orderShippingLogisticsCommand);

        return $order;
    }

}