<?php

namespace RedJasmine\Order\Tests\Application\Order\CommandHandlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Tests\Application\ApplicationTest;
use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;

class OrderCreateCommandHandlerTest extends ApplicationTest
{


    /**
     * @test 创建订单
     * 前提条件: 准备订单数据
     * 步骤：
     *  1、创建数据
     *  2、执行
     *  3、
     * 预期结果:
     *  1、有返回结果
     *  2、
     * @return void
     */
    public function can_create_order() : void
    {

        $fake               = new OrderFake();
        $orderCreateCommand = OrderCreateCommand::from($fake->fake());

        $order              = $this->orderCommandService()->create($orderCreateCommand);
        $this->assertInstanceOf(Order::class, $order);

    }

}
