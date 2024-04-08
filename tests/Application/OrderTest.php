<?php

namespace RedJasmine\Order\Tests\Application;


use RedJasmine\Order\Domains\Order\Application\Data\OrderData;
use RedJasmine\Order\Domains\Order\Application\Services\OrderService;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCancelCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Tests\TestCase;

class OrderTest extends TestCase
{

    protected function fakeAddressArray() : array
    {
        return [
            'contacts'   => fake()->name,
            'mobile'     => fake()->phoneNumber(),
            'country'    => fake()->country(),
            'province'   => fake()->city(),
            'city'       => fake()->city(),
            'district'   => fake()->city,
            'street'     => fake()->streetName(),
            'address'    => fake()->address(),
            'zip_code'   => fake()->numerify('######'),
            'lon'        => fake()->longitude(),
            'lat'        => fake()->latitude(),
            'countryId'  => 0,
            'provinceId' => 110000,
            'cityId'     => 111100,
            'districtId' => 111111,
            'streetId'   => null,
            'extends'    => [],

        ];

    }

    protected function fakeOrderArray(array $order = []) : array
    {
        $fake = [
            'buyer'          => [
                'type' => 'buyer',
                'id'   => fake()->numberBetween(1000000, 999999999),
            ],
            'seller'         => [
                'type' => 'seller',
                'id'   => fake()->numberBetween(1000000, 999999999),
            ],
            'title'          => fake()->name,
            'order_type'     => OrderTypeEnum::MALL->value,
            'shipping_type'  => ShippingTypeEnum::EXPRESS->value,
            'source'         => fake()->randomElement([ 'product', 'activity' ]),
            'outer_order_id' => fake()->numerify('out-order-id-########'),
            //'channel_type'    => fake()->randomElement([ 'channel', 'promoter' ]),
            //'channel_id'      => fake()->randomNumber(5, true),
            'channel'        => [
                'type' => fake()->randomElement([ 'channel', 'promoter' ]),
                'id'   => fake()->randomNumber(5, true),
            ],
            //'store_type'      => fake()->randomElement([ 'self', 'franchise' ]),
            //'store_id'        => fake()->randomNumber(5, true),
            'store'          => [
                'type' => fake()->randomElement([ 'self', 'franchise' ]),
                'id'   => fake()->randomNumber(5, true),
            ],
            'guide'          => [
                'type' => fake()->randomElement([ 'user', 'promoter', 'seller' ]),
                'id'   => fake()->randomNumber(5, true),
            ],

            'freight_amount'  => fake()->randomFloat(0, 0, 20),
            'discount_amount' => fake()->randomFloat(0, 5, 10),
            'contact'         => fake()->phoneNumber(),
            'password'        => fake()->password(6),
            'client_type'     => fake()->randomElement([ 'h5', 'ios-app', 'applets' ]),
            'client_ip'       => fake()->ipv4(),
            'info'            => [
                'seller_remarks' => fake()->sentence(10),
                'seller_message' => fake()->sentence(10),
                'buyer_remarks'  => fake()->sentence(10),
                'buyer_message'  => fake()->sentence(10),
                'seller_extends' => [],
                'buyer_extends'  => [],
                'other_extends'  => [],
                'tools'          => [],
            ],
            'address'         => $this->fakeAddressArray(),
        ];
        return array_merge($fake, $order);
    }

    protected function fakeProductArray(array $product = []) : array
    {
        $fake = [
            'shipping_type'          => ShippingTypeEnum::EXPRESS->value,
            'order_product_type'     => fake()->randomElement([ 'goods' ]),
            'title'                  => fake()->sentence(),
            'sku_name'               => fake()->words(1, true),
            'image'                  => fake()->imageUrl,
            'product_type'           => 'product',
            'product_id'             => fake()->numberBetween(1000000, 999999999),
            'sku_id'                 => fake()->numberBetween(1000000, 999999999),
            'category_id'            => null,
            'seller_category_id'     => null,
            'outer_id'               => fake()->numerify('out-id-########'),
            'outer_sku_id'           => fake()->numerify('out-sku-id-########'),
            'barcode'                => fake()->ean13(),
            'num'                    => fake()->numberBetween(1, 200),
            'price'                  => fake()->randomFloat(2, 90, 100),
            'cost_price'             => fake()->randomFloat(2, 70, 80),
            'tax_amount'             => fake()->randomFloat(2, 10, 20),
            'discount_amount'        => fake()->randomFloat(2, 5, 20),
            'outer_order_product_id' => fake()->numerify('CODE-########'),
            'info'                   => [
                'seller_remarks' => fake()->sentence(10),
                'seller_message' => fake()->sentence(10),
                'buyer_remarks'  => fake()->sentence(10),
                'buyer_message'  => fake()->sentence(10),
                'seller_extends' => [],
                'buyer_extends'  => [],
                'other_extends'  => [],
                'tools'          => [],
            ],
        ];
        return array_merge($fake, $product);
    }

    protected function service() : OrderService
    {
        return app(OrderService::class);
    }

    protected function orderRepository() : OrderRepositoryInterface
    {
        return app(OrderRepositoryInterface::class);
    }


    public function test_order_create()
    {
        $orderDataArray               = $this->fakeOrderArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderCreateCommand           = OrderCreateCommand::from($orderDataArray);

        $orderDTO = $this->service()->create($orderCreateCommand);

        $this->assertInstanceOf(OrderData::class, $orderDTO);
        // TODO

        return $orderDTO;
    }


    public function test_order_paying() : array
    {

        $orderData = $this->test_order_create();


        $command = OrderPayingCommand::from([
                                                'id'          => $orderData->id,
                                                'amount'      => $orderData->payableAmount,
                                                'amount_type' => 'all'
                                            ]);


        $orderPaymentID = $this->service()->paying($command);

        $order = $this->orderRepository()->find($command->id);
        $this->assertIsNumeric($orderPaymentID);
        $orderPayment = $order->payments->where('id', $orderPaymentID)->first();

        $this->assertEquals($order->payable_amount, $orderPayment?->payment_amount);
        $this->assertEquals(PaymentStatusEnum::PAYING->value, $order->payment_status->value);
        $this->assertEquals(PaymentStatusEnum::PAYING->value, $orderPayment?->status->value);
        return [
            'id'               => $command->id,
            'order_payment_id' => $orderPaymentID,
            'amount'           => $orderPayment?->payment_amount,
        ];
    }


    public function test_order_paid() : \RedJasmine\Order\Domains\Order\Domain\Models\Order
    {
        $data           = $this->test_order_paying();
        $id             = $data['id'];
        $orderPaymentId = $data['order_payment_id'];

        $command = OrderPaidCommand::from([
                                              'id'                 => $id,
                                              'order_payment_id'   => $orderPaymentId,
                                              'amount'             => $data['amount'],
                                              'payment_type'       => fake()->randomElement([ 'offline', 'payment_center' ]),
                                              'payment_id'         => fake()->numberBetween(1000000, 999999999),
                                              'payment_channel'    => fake()->randomElement([ 'alipay', 'wechat', 'yi', 'union_pay' ]),
                                              'payment_channel_no' => fake()->numberBetween(1000000, 999999999),
                                              'payment_method'     => fake()->randomElement([ 'alipay', 'wechat', 'cash', 'bank' ]),
                                              'payment_time'       => now()
                                          ]);


        $this->service()->paid($command);

        $order        = $this->orderRepository()->find($id);
        $orderPayment = $order->payments->where('id', $orderPaymentId)->first();

        $this->assertEquals(PaymentStatusEnum::PAID->value, $orderPayment->status->value, '支付单状态错误');
        $this->assertEquals($command->paymentType, $orderPayment->payment_type);
        $this->assertEquals($command->paymentId, $orderPayment->payment_id);
        $this->assertEquals($command->paymentChannel, $orderPayment->payment_channel);
        $this->assertEquals($command->paymentChannelNo, $orderPayment->payment_channel_no);
        $this->assertEquals($command->paymentMethod, $orderPayment->payment_method);

        $this->assertEquals(PaymentStatusEnum::PAID->value, $order->payment_status->value, '支付状态错误');

        $this->assertEquals($order->payable_amount, $order->payment_amount, '实付金额不一致');

        return $order;
    }


    public function test_order_cancel() : void
    {
        $data = $this->test_order_create();

        $command = OrderCancelCommand::from([ 'id' => $data->id, 'cancel_reason' => '不想要了' ]);

        $this->service()->cancel($command);

        $order = $this->orderRepository()->find($data->id);

        $this->assertEquals(OrderStatusEnum::CANCEL->value, $order->order_status->value);
        $this->assertEquals($command->cancelReason, $order->cancel_reason);
        $order->products->each(function ($product) use ($command) {
            $this->assertEquals(OrderStatusEnum::CANCEL->value, $product->order_status->value);
        });

    }


    // 测试发货流程
    public function test_order_shipping_logistics() : void
    {
        $order = $this->test_order_paid();


        $command = OrderShippingLogisticsCommand::from([
                                                           'id'                   => $order->id,
                                                           'is_split'             => false,
                                                           'express_company_code' => 'shunfeng',
                                                           'express_no'           => fake()->numerify('##########')
                                                       ]);

        $this->service()->shippingLogistics($command);

        $order = $this->orderRepository()->find($command->id);

        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);

        $order->products->each(function ($product) use ($command) {
            $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product->shipping_status->value);
        });

        $logistics = $order->logistics->first();
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals($command->expressNo, $logistics->express_no);

    }


    public function test_order_part_shipping_logistics()

    {
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


        $this->service()->shippingLogistics($command);

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

        $order = $this->orderRepository()->find($command->id);

        // TODO 有问题
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value);


        $order->products->each(function ($product) use ($command, $shippingOrderProducts) {
            if (in_array($product->id, $shippingOrderProducts, true)) {
                $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $product?->shipping_status?->value);
            }
        });

        $logistics = $order->logistics->first();
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals($command->expressNo, $logistics->express_no);
        $this->assertEquals($command->orderProducts, $logistics->order_product_id);


    }

}
