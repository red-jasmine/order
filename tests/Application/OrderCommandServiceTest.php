<?php

namespace RedJasmine\Order\Tests\Application;


use RedJasmine\Order\Application\UserCases\Commands\OrderCancelCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderConfirmCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderProgressCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderHiddenCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderRemarksCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderSellerCustomStatusCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand;
use RedJasmine\Order\Domain\Enums\OrderCardKeyStatusEnum;
use RedJasmine\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderProduct;
use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;


class OrderCommandServiceTest extends OrderBase
{




    public function test_order_cancel() : void
    {
        $data = $this->test_order_create();

        $command = OrderCancelCommand::from([ 'id' => $data->id, 'cancel_reason' => '不想要了' ]);

        $this->orderService()->cancel($command);

        $order = $this->orderRepository()->find($data->id);

        $this->assertEquals(OrderStatusEnum::CANCEL->value, $order->order_status->value);
        $this->assertEquals($command->cancelReason, $order->cancel_reason);
        $order->products->each(function ($product) use ($command) {
            $this->assertEquals(OrderStatusEnum::CANCEL->value, $product->order_status->value);
        });

    }


    // 测试发货流程
    public function test_order_shipping_logistics() : Order
    {
        $order = $this->test_order_paid();


        $command = OrderShippingLogisticsCommand::from([
                                                           'id'                   => $order->id,
                                                           'is_split'             => false,
                                                           'express_company_code' => 'shunfeng',
                                                           'express_no'           => fake()->numerify('##########')
                                                       ]);

        $this->orderService()->shippingLogistics($command);

        $order = $this->orderRepository()->find($command->id);

        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);

        $order->products->each(function ($product) use ($command) {
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product->shipping_status->value);
        });

        $logistics = $order->logistics->first();
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals($command->expressNo, $logistics->express_no);

        return $order;

    }


    public function test_order_part_shipping_logistics()
    {

        // 部分发货
        $order         = $this->test_order_paid();
        $orderProducts = $order->products->pluck('id')->toArray();


        $shippingOrderProducts = [ $orderProducts[0], $orderProducts[1] ];
        $command               = OrderShippingLogisticsCommand::from([
                                                                         'id'                   => $order->id,
                                                                         'is_split'             => true,
                                                                         'order_products'       => $shippingOrderProducts,
                                                                         'express_company_code' => 'shunfeng',
                                                                         'express_no'           => fake()->numerify('##########')
                                                                     ]);


        $this->orderService()->shippingLogistics($command);

        $order = $this->orderRepository()->find($command->id);

        $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $order->shipping_status->value);


        $order->products->each(function ($product) use ($command, $shippingOrderProducts) {
            if (in_array($product->id, $shippingOrderProducts, true)) {
                $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product?->shipping_status?->value);
            }
        });

        $logistics = $order->logistics->first();
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals($command->expressNo, $logistics->express_no);
        $this->assertEquals($command->orderProducts, $logistics->order_product_id);


        // 完成发货
        $shippingOrderProducts  = [ $orderProducts[2] ];
        $command->orderProducts = $shippingOrderProducts;

        $this->orderService()->shippingLogistics($command);

        $order = $this->orderRepository()->find($command->id);


        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);


        $order->products->each(function ($product) use ($command, $shippingOrderProducts) {
            if (in_array($product->id, $shippingOrderProducts, true)) {
                $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product?->shipping_status?->value);
            }
        });

        $logistics = $order->logistics->last();
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals($command->expressNo, $logistics->express_no);
        $this->assertEquals($command->orderProducts, $logistics->order_product_id);


        return $order;
    }


    public function test_order_shipping_card_key()
    {
        $order = $this->test_order_paid();

        $orderProducts = $order->products->pluck('id')->toArray();


        foreach ($order->products as $orderProduct) {
            $shippingOrderProductId = $orderProduct->id;
            $command                = OrderShippingCardKeyCommand::from([
                                                                            'id'               => $order->id,
                                                                            'order_product_id' => $shippingOrderProductId,
                                                                            'content'          => fake()->numerify('##########'),
                                                                            'status'           => OrderCardKeyStatusEnum::SHIPPED->value
                                                                        ]);
            $this->orderService()->shippingCardKey($command);
        }


        $order = $this->orderRepository()->find($order->id);
        $order->products->each(function (OrderProduct $orderProduct) {
            $this->assertEquals(1, $orderProduct->progress);
            $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $orderProduct?->shipping_status?->value);
        });


        foreach ($order->products as $orderProduct) {
            $shippingOrderProductId = $orderProduct->id;
            $command                = OrderShippingCardKeyCommand::from([
                                                                            'id'               => $order->id,
                                                                            'order_product_id' => $shippingOrderProductId,
                                                                            'content'          => fake()->numerify('##########'),
                                                                            'status'           => OrderCardKeyStatusEnum::SHIPPED->value
                                                                        ]);

            for ($i = 1; $i <= $orderProduct->num - 1; $i++) {
                $this->orderService()->shippingCardKey($command);
            }
        }

        $order = $this->orderRepository()->find($order->id);

        $order->products->each(function (OrderProduct $orderProduct) {
            $this->assertEquals($orderProduct->num, $orderProduct->progress);
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $orderProduct?->shipping_status?->value);
        });

        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);


    }


    public function test_order_shipping_virtual()
    {
        $order = $this->test_order_paid();

        foreach ($order->products as $orderProduct) {
            $command = OrderShippingVirtualCommand::from([
                                                             'id'               => $order->id,
                                                             'order_product_id' => $orderProduct->id,
                                                         ]);

            $this->orderService()->shippingVirtual($command);
        }

        $order = $this->orderRepository()->find($order->id);
        $order->products->each(function (OrderProduct $orderProduct) {
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $orderProduct?->shipping_status?->value);
        });

        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);


    }


    public function test_order_confirm()
    {

        $order = $this->test_order_shipping_logistics();


        $command = OrderConfirmCommand::from([ 'id' => $order->id ]);


        $this->orderService()->confirm($command);


        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals(OrderStatusEnum::FINISHED->value, $order->order_status->value);

        $order->products->each(function ($product) {
            $this->assertEquals(OrderStatusEnum::FINISHED->value, $product->order_status->value);

        });


    }


    public function test_order_progress()
    {
        $order = $this->test_order_shipping_logistics();

        $order_product_id = $order->products[0]->id;
        $command          = OrderProgressCommand::from([
                                                           'id'               => $order->id,
                                                           'order_product_id' => $order_product_id,
                                                           'progress'         => fake()->numberBetween(1, 100),
                                                           'progress_total'   => 100,
                                                       ]);


        $this->orderService()->progress($command);

        $order        = $this->orderRepository()->find($order->id);
        $orderProduct = $order->products[0];


        $this->assertEquals($command->progress, $orderProduct->progress);
        $this->assertEquals($command->progressTotal, $orderProduct->progress_total);


        $command2 = OrderProgressCommand::from([
                                                   'id'               => $order->id,
                                                   'order_product_id' => $order_product_id,
                                                   'progress'         => fake()->numberBetween(1, 100),
                                                   // 'progress_total'   => 100,
                                               ]);


        $this->orderService()->progress($command2);


        $order        = $this->orderRepository()->find($order->id);
        $orderProduct = $order->products[0];
        $this->assertEquals($command2->progress, $command2->progress);
        $this->assertEquals($command->progressTotal, $orderProduct->progress_total);


    }


    // 其他操作测试

    public function test_remarks()
    {
        $order = $this->test_order_create();

        $command = OrderRemarksCommand::from([
                                                 'id'      => $order->id,
                                                 'remarks' => fake()->text,
                                             ]);

        $this->orderService()->sellerRemarks($command);
        $this->orderService()->buyerRemarks($command);

        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals($command->remarks, $order->info->seller_remarks);
        $this->assertEquals($command->remarks, $order->info->buyer_remarks);

    }


    public function test_hidden_order()
    {
        $order = $this->test_order_create();
        $order = $this->orderRepository()->find($order->id);
        $this->assertEquals(false, $order->is_buyer_delete);
        $this->assertEquals(false, $order->is_seller_delete);

        $command = OrderHiddenCommand::from([
                                                'id' => $order->id,
                                            ]);


        $this->orderService()->buyerHidden($command);
        $order = $this->orderRepository()->find($order->id);
        $this->assertEquals(true, $order->is_buyer_delete);
        $this->assertEquals(false, $order->is_seller_delete);


        $this->orderService()->sellerHidden($command);

        $order = $this->orderRepository()->find($order->id);
        $this->assertEquals(true, $order->is_buyer_delete);
        $this->assertEquals(true, $order->is_seller_delete);

    }


    public function test_order_seller_custom_status()
    {
        $order          = $this->test_order_paid();
        $orderProductId = $order->products[0]->id;
        $command        = OrderSellerCustomStatusCommand::from([
                                                                   'id'                   => $order->id,
                                                                   'seller_custom_status' => fake('en')->word(),
                                                               ]);

        $this->orderService()->sellerCustomStatus($command);

        $order = $this->orderRepository()->find($order->id);

        $this->assertEquals($command->sellerCustomStatus, $order->seller_custom_status);


        $command = OrderSellerCustomStatusCommand::from([
                                                            'id'                   => $order->id,
                                                            'seller_custom_status' => fake('en')->word(),
                                                            'order_product_id'     => $orderProductId,
                                                        ]);

        $this->orderService()->sellerCustomStatus($command);

        $order = $this->orderRepository()->find($order->id);

        $orderProduct = $order->products[0];

        $this->assertEquals($command->sellerCustomStatus, $orderProduct->seller_custom_status);


    }
}
