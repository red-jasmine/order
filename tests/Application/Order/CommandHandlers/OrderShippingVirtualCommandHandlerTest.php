<?php

namespace Order\CommandHandlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Tests\Application\ApplicationTestCase;

class OrderShippingVirtualCommandHandlerTest extends ApplicationTestCase
{


    protected function orderPaid() : Order
    {
        // 1、创建订单
        $fake               = $this->fake();
        $fake->productCount = 2;
        $fake->shippingType = ShippingTypeEnum::VIRTUAL;
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
     * @test 订单能虚拟发货
     * 前提条件: 已支付虚拟发货订单
     * 步骤：
     *  1、对所有订单进行虚拟发货
     *
     * 预期结果:
     *  1、订单状态 等待买家确认、已发货
     * @return void
     */
    public function can_shipping_virtual() : void
    {


        $order = $this->orderPaid();
        //1、所有 子商品单 卡密发货

        foreach ($order->products as $product) {
            $orderShippingVirtualCommand = $this->fake()->shippingVirtual([
                                                                              'id'               => $order->id,
                                                                              'order_product_id' => $product->id,
                                                                              'is_finished'      => true,
                                                                          ]);

            $this->orderCommandService()->shippingVirtual($orderShippingVirtualCommand);
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


    /**
     * @test 订单能支持分阶段发货
     * 前提条件: 虚拟商品订单已支付
     * 步骤：
     *  1、第一次 标记发货
     *  2、第二次 标记已发货
     *  3、
     * 预期结果:
     *  1、第一次 子商品单状态为 部分发货
     *  2、第二次 子商品单状态为 已发货
     * @return void
     */
    public function can_stage_shipping_virtual() : void
    {

        $order = $this->orderPaid();
        //1、第一次发货 标记还没有完成

        foreach ($order->products as $product) {
            $orderShippingVirtualCommand = $this->fake()->shippingVirtual([
                                                                              'id'               => $order->id,
                                                                              'order_product_id' => $product->id,
                                                                              'is_finished'      => false,
                                                                          ]);

            $this->orderCommandService()->shippingVirtual($orderShippingVirtualCommand);
        }


        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态');
        $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $order->shipping_status->value, '发货状态');
        $this->assertNotEmpty($order->shipping_time);

        foreach ($order->products as $product) {
            $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $product->order_status->value, '子商品单状态');
            $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $product->shipping_status->value, '发货状态');
            $this->assertNotEmpty($product->shipping_time);
        }


        // 第二次发货

        foreach ($order->products as $product) {
            $orderShippingVirtualCommand = $this->fake()->shippingVirtual([
                                                                              'id'               => $order->id,
                                                                              'order_product_id' => $product->id,
                                                                              'is_finished'      => true,
                                                                          ]);

            $this->orderCommandService()->shippingVirtual($orderShippingVirtualCommand);
        }


        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '二次发货后订单状态');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态');
        $this->assertNotEmpty($order->shipping_time);

        foreach ($order->products as $product) {
            $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $product->order_status->value, '商品单状态');
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product->shipping_status->value, '发货状态');
            $this->assertNotEmpty($product->shipping_time);
        }


    }

}
