<?php

namespace Order\CommandHandlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Tests\Application\ApplicationTest;

class OrderShippingCardKeyCommandHandlerTest extends ApplicationTest
{


    protected function orderPaid() : Order
    {
        // 1、创建订单
        $fake               = $this->fake();
        $fake->productCount = 2;
        $fake->shippingType = ShippingTypeEnum::CDK;

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
     * @test 订单能卡密发货
     * 前提条件: 已支付卡密订单
     * 步骤：
     *  1、对所有订单进行卡密发货
     *
     * 预期结果:
     *  1、
     * @return void
     */
    public function can_shipping_card_key() : void
    {


        $order = $this->orderPaid();
        //1、所有 子商品单 卡密发货

        foreach ($order->products as $product) {
            $orderShippingCardKeyCommand = $this->fake()->shippingCardKey([
                                                                              'id'               => $order->id,
                                                                              'order_product_id' => $product->id,
                                                                              'num'              => $product->num
                                                                          ]);


            $this->orderCommandService()->shippingCardKey($orderShippingCardKeyCommand);
        }


        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '订单状态');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态');
        $this->assertNotEmpty($order->shipping_time);

        foreach ($order->products as $product) {
            $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $product->order_status->value, '订单状态');
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product->shipping_status->value, '发货状态');
            $this->assertNotEmpty($product->shipping_time);
        }

    }


}
