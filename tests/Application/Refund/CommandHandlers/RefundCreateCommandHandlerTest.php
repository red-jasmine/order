<?php

namespace RedJasmine\Order\Tests\Application\Refund\CommandHandlers;

use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Order\Tests\Application\ApplicationTest;

class RefundCreateCommandHandlerTest extends ApplicationTest
{


    protected function orderPaid() : Order
    {
        // 1、创建订单
        $fake               = $this->fake();
        $fake->productCount = 2;


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


    public function orderPaidAndShipping() : Order
    {
        $order = $this->orderPaid();

        $orderShippingLogisticsCommand = $this->fake()->shippingLogistics([
                                                                              'id' => $order->id
                                                                          ]);


        $this->orderCommandService()->shippingLogistics($orderShippingLogisticsCommand);


        return $order;
    }


    /**
     * @test 能创建订单
     * 前提条件: 订单已支付
     * 步骤：
     *  1、发起一个产品的 仅退款的订单
     *  2、
     *  3、
     * 预期结果:
     *  1、创建退款单成功
     *  2、
     * @return void
     */
    public function can_create_only_refund()
    {
        // 前提条件
        $order = $this->orderPaid();

        // 1、创建退款单
        foreach ($order->products as $product) {
            $command = RefundCreateCommand::from([
                                                     'id'               => $order->id,
                                                     'order_product_id' => $product->id,
                                                     'refund_type'      => RefundTypeEnum::REFUND->value,
                                                     'reason'           => fake()->randomElement([ '不想要了', '拍错了' ]),
                                                     'refund_amount'    => null,
                                                     'description'      => fake()->text,
                                                     'outer_refund_id'  => fake()->numerify('##########'),
                                                     'images'           => [ fake()->imageUrl, fake()->imageUrl, fake()->imageUrl, ],
                                                 ]);


            $refundId = $this->refundCommandService()->create($command);
            $refund   = $this->refundRepository()->find($refundId);


            $this->assertEquals($order->id, $refund->order_id);
            $this->assertEquals($product->id, $refund->order_product_id);

            $this->assertEquals($command->refundType, $refund->refund_type);
            $this->assertEquals(RefundStatusEnum::WAIT_SELLER_AGREE->value, $refund->refund_status->value);

        }


    }

    /**
     * @test 能创建 退货退款单
     * 前提条件:
     * 步骤：
     *  1、
     *  2、
     *  3、
     * 预期结果:
     *  1、
     *  2、
     * @return void
     */
    public function can_crate_return_goods_refund():void
    {

        $order = $this->orderPaidAndShipping();


        // 创建退货退款单
        foreach ($order->products as $product) {
            $command = RefundCreateCommand::from([
                                                     'id'               => $order->id,
                                                     'order_product_id' => $product->id,
                                                     'refund_type'      => RefundTypeEnum::RETURN_GOODS_REFUND->value,
                                                     'reason'           => fake()->randomElement([ '不想要了', '拍错了' ]),
                                                     'refund_amount'    => null,
                                                     'description'      => fake()->text,
                                                     'outer_refund_id'  => fake()->numerify('##########'),
                                                     'images'           => [ fake()->imageUrl, fake()->imageUrl, fake()->imageUrl, ],
                                                 ]);


            $refundId = $this->refundCommandService()->create($command);
            $refund   = $this->refundRepository()->find($refundId);


            $this->assertEquals($order->id, $refund->order_id);
            $this->assertEquals($product->id, $refund->order_product_id);

            $this->assertEquals($command->refundType, $refund->refund_type);
            $this->assertEquals(RefundStatusEnum::WAIT_SELLER_AGREE_RETURN->value, $refund->refund_status->value);

        }


    }


}
