<?php

namespace RedJasmine\Order\Tests\Feature;

use RedJasmine\Order\Services\Order\Data\OrderProductProgressData;
use RedJasmine\Order\Services\Order\Data\OrderRemarksData;
use RedJasmine\Order\Services\Order\Data\OrderSellerCustomStatusData;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderVirtualShippingData;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Data\OrderData;
use RedJasmine\Order\Services\Order\Data\OrderPaidInfoData;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderCardKeyShippingData;
use RedJasmine\Order\Services\Order\Data\Shipping\OrderLogisticsShippingData;
use RedJasmine\Order\Services\Order\Enums\OrderTypeEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingTypeEnum;
use RedJasmine\Order\Services\Order\OrderService;
use RedJasmine\Order\Tests\TestCase;

class OrderTe2st extends TestCase
{


    protected function fakeAddressArray() : array
    {
        return [
            'contacts'   => fake()->title,
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
                'id'   => 1,
            ],
            'seller'         => [
                'type' => 'buyer',
                'id'   => 1,
            ],
            'title'          => fake()->title(),
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
            'order_product_type'     => fake()->randomElement([ 'goods', 'card' ]),
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


    /**
     * 创建订单
     * @return Order
     */
    public function test_create_for_array() : Order
    {
        $orderDataArray               = $this->fakeOrderArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderData                    = OrderData::from($orderDataArray);
        $result                       = $this->service()->create($orderData->toArray());
        $this->assertInstanceOf(Order::class, $result, '');
        $this->assertModelExists($result);

        // 创建订单验证 TODO

        return $result;
    }


    /**
     * @depends test_create_for_array
     * @return void
     */
    public function test_order_cancel(Order $order)
    {

        $cancel_reason = '不想要了';
        /**
         * @var Order $order
         */
        $order = $this->service()->cancel($order->id, [ 'cancel_reason' => $cancel_reason ]);
        $order->refresh();
        $this->assertEquals(OrderStatusEnum::CANCEL->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals($cancel_reason, $order->cancel_reason, '取消原因错误');
        $this->assertNotNull($order->close_time, '取消原因错误');
        $order->products->each(function (OrderProduct $orderProduct) {
            $this->assertEquals(OrderStatusEnum::CANCEL->value, $orderProduct->order_status->value, '商品参数错误');
        });
    }

    public function test_order_paid(array $data = [])
    {
        // 创建订单
        $orderDataArray               = $this->fakeOrderArray($data['order'] ?? []);
        $orderDataArray['products'][] = $this->fakeProductArray($data['product'] ?? []);
        $orderDataArray['products'][] = $this->fakeProductArray($data['product'] ?? []);
        $orderData                    = OrderData::from($orderDataArray);

        $order = $this->service()->create($orderData->toArray());

        $orderPaidInfoData = OrderPaidInfoData::from([
                                                         'payment_amount'  => $order->payable_amount,
                                                         'payment_type'    => fake()->randomElement([ 'payment', 'cash' ]),
                                                         'payment_id'      => fake()->numberBetween(1000000, 999999999),
                                                         'payment_channel' => fake()->randomElement([ 'alipay', 'wechat' ]),
                                                     ]);

        // 发起支付
        /**
         * @var Order $order
         */
        $order = $this->service()->paid($order->id, $orderPaidInfoData);
        $order->refresh();
        $this->assertEquals(PaymentStatusEnum::PAID->value, $order->payment_status->value, '支付状态错误');
        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::WAIT_SEND->value, $order->shipping_status->value, '发货状态');
        $this->assertEquals($order->payable_amount, $order->payment_amount, '支付金额错误');
        $order->products->each(function (OrderProduct $orderProduct) use ($order) {
            $this->assertEquals($order->order_status->value, $orderProduct->order_status->value, '商品订单状态错误');
            $this->assertEquals($order->payment_status->value, $orderProduct->payment_status->value, '商品支付状态错误');
            $this->assertEquals($order->shipping_status->value, $orderProduct->shipping_status->value, '商品发货状态错误');
        });

        return $order;
    }


    public function test_order_part_paid()
    {
        // 创建订单
        $orderDataArray               = $this->fakeOrderArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderData                    = OrderData::from($orderDataArray);

        $order = $this->service()->create($orderData->toArray());

        $orderPaidInfoData = OrderPaidInfoData::from([
                                                         'payment_amount'  => bcsub($order->payable_amount, 10, 2),
                                                         'payment_type'    => fake()->randomElement([ 'payment', 'cash' ]),
                                                         'payment_id'      => fake()->numberBetween(1000000, 999999999),
                                                         'payment_channel' => fake()->randomElement([ 'alipay', 'wechat' ]),
                                                     ]);

        // 发起支付
        /**
         * @var Order $order
         */
        $order = $this->service()->paid($order->id, $orderPaidInfoData);
        $order->refresh();
        $this->assertEquals(PaymentStatusEnum::PART_PAY->value, $order->payment_status->value, '支付状态错误');
        $this->assertEquals(bcsub($order->payable_amount, 10, 2), $order->payment_amount, '支付金额错误');
        $order->products->each(function (OrderProduct $orderProduct) use ($order) {
            $this->assertEquals($order->payment_status->value, $orderProduct->payment_status->value, '商品支付状态错误');
        });

        // 支付剩余的
        $orderPaidInfoData->paymentAmount = 10;
        $order                            = $this->service()->paid($order->id, $orderPaidInfoData);
        $order->refresh();
        $this->assertEquals(PaymentStatusEnum::PAID->value, $order->payment_status->value, '支付状态错误');
        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::WAIT_SEND->value, $order->shipping_status->value, '发货状态');
        $this->assertEquals($order->payable_amount, $order->payment_amount, '支付金额错误');
        $order->products->each(function (OrderProduct $orderProduct) use ($order) {
            $this->assertEquals($order->order_status->value, $orderProduct->order_status->value, '商品订单状态错误');
            $this->assertEquals($order->payment_status->value, $orderProduct->payment_status->value, '商品支付状态错误');
            $this->assertEquals($order->shipping_status->value, $orderProduct->shipping_status->value, '商品发货状态错误');
        });


        // 再次支付
        $orderPaidInfoData->paymentAmount = 10;
        $this->expectException(OrderException::class);
        $order = $this->service()->paid($order->id, $orderPaidInfoData);


    }


    /**
     * 测试 物流发货
     * @depends test_order_paid
     *
     * @return void
     */
    public function test_order_shipping_logisitcs()
    {
        // 已支付的订单
        $order = $this->test_order_paid();

        //发货
        $orderLogisticsShippingData = OrderLogisticsShippingData::from([
                                                                           'is_split'             => false,
                                                                           'express_company_code' => 'yuantong',
                                                                           'express_no'           => fake()->numerify('NO-########'),
                                                                       ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->logisticsShipping($order->id, $orderLogisticsShippingData);
        // 断言
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(1, $order->logistics()->count(), '物流');

        $this->assertEquals($orderLogisticsShippingData->expressCompanyCode, $order->logistics[0]->express_company_code);
        $this->assertEquals($orderLogisticsShippingData->expressNo, $order->logistics[0]->express_no);
        $this->assertEquals($order->buyer_type, $order->logistics[0]->buyer_type);
        $this->assertEquals($order->buyer_id, $order->logistics[0]->buyer_id);
        $this->assertEquals($order->seller_type, $order->logistics[0]->seller_type);
        $this->assertEquals($order->seller_id, $order->logistics[0]->seller_id);

    }


    /*
     * 测试物流部分发货
     *
     */
    public function test_order_part_shipping_logisitcs()
    {
        // 已支付的订单
        $order = $this->test_order_paid();

        $orderProducts = [ $order->products[0]->id ];

        //发货
        $orderLogisticsShippingData = OrderLogisticsShippingData::from([
                                                                           'is_split'             => true,
                                                                           'express_company_code' => 'yuantong',
                                                                           'express_no'           => fake()->numerify('NO-########'),
                                                                           'order_products'       => $orderProducts
                                                                       ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->logisticsShipping($order->id, $orderLogisticsShippingData);
        // 断言
        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(1, $order->logistics()->count(), '物流');

        $this->assertEquals($orderLogisticsShippingData->expressCompanyCode, $order->logistics[0]->express_company_code);
        $this->assertEquals($orderLogisticsShippingData->expressNo, $order->logistics[0]->express_no);
        $this->assertEquals($order->buyer_type, $order->logistics[0]->buyer_type);
        $this->assertEquals($order->buyer_id, $order->logistics[0]->buyer_id);
        $this->assertEquals($order->seller_type, $order->logistics[0]->seller_type);
        $this->assertEquals($order->seller_id, $order->logistics[0]->seller_id);


        // 再次发货

        $orderProducts              = [ $order->products[1]->id ];
        $orderLogisticsShippingData = OrderLogisticsShippingData::from([
                                                                           'is_split'             => true,
                                                                           'express_company_code' => 'yuantong',
                                                                           'express_no'           => fake()->numerify('NO-########'),
                                                                           'order_products'       => $orderProducts
                                                                       ]);


        /**
         * @var Order $order
         */
        $order = $this->service()->logisticsShipping($order->id, $orderLogisticsShippingData);
        // 断言
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(2, $order->logistics()->count(), '物流单数量不正取');
        $order->logistics;
        $this->assertEquals($orderLogisticsShippingData->expressCompanyCode, $order->logistics[1]->express_company_code);
        $this->assertEquals($orderLogisticsShippingData->expressNo, $order->logistics[1]->express_no);
        $this->assertEquals($order->buyer_type, $order->logistics[1]->buyer_type);
        $this->assertEquals($order->buyer_id, $order->logistics[1]->buyer_id);
        $this->assertEquals($order->seller_type, $order->logistics[1]->seller_type);
        $this->assertEquals($order->seller_id, $order->logistics[1]->seller_id);

    }


    public function test_order_card_key_shipping()
    {
        $data              = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::CDK,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::CDK,
            ],
        ];
        $order = $this->test_order_paid($data);
        $orderProducts = [ $order->products[0]->id ];
        $orderShippingData = OrderCardKeyShippingData::from([
                                                                'is_split'       => true,
                                                                'order_products' => $orderProducts,
                                                                'contents'        => [
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                ],
                                                            ]);


       ;
        /**
         * @var Order $order
         */
        $order = $this->service()->cardKeyShipping($order->id, $orderShippingData);
        $order->refresh();
        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $order->shipping_status->value, '发货状态错误');

        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->products[0]->order_status->value);
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->products[0]->shipping_status->value);

        $orderProducts     = [ $order->products[1]->id ];
        $orderShippingData = OrderCardKeyShippingData::from([
                                                                'is_split'       => true,
                                                                'order_products' => $orderProducts,
                                                                'contents'        => [
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                    [
                                                                        'content' => fake()->text(),
                                                                    ],
                                                                ],
                                                            ]);

        /**
         * @var Order $order
         */
        $order = $this->service()->cardKeyShipping($order->id, $orderShippingData);
        $order->refresh();
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->products[1]->order_status->value);
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->products[1]->shipping_status->value);

    }


    public function test_order_virtual_shipping()
    {
        $data              = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order             = $this->test_order_paid($data);
        $orderProducts     = [ $order->products[0]->id ];
        $orderShippingData = OrderVirtualShippingData::from([
                                                                'is_split'       => true,
                                                                'order_products' => $orderProducts,
                                                                // todo .....
                                                            ]);

        /**
         * @var Order $order
         */
        $order = $this->service()->virtualShipping($order->id, $orderShippingData);
        $order->refresh();
        $this->assertEquals(OrderStatusEnum::WAIT_SELLER_SEND_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::PART_SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->products[0]->order_status->value);
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->products[0]->shipping_status->value);

        $orderProducts     = [ $order->products[1]->id ];
        $orderShippingData = OrderVirtualShippingData::from([
                                                                'is_split'       => true,
                                                                'order_products' => $orderProducts,
                                                                // todo .....
                                                            ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->virtualShipping($order->id, $orderShippingData);
        $order->refresh();
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->order_status->value, '订单状态错误');
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->shipping_status->value, '发货状态错误');
        $this->assertEquals(OrderStatusEnum::WAIT_BUYER_CONFIRM_GOODS->value, $order->products[1]->order_status->value);
        $this->assertEquals(ShippingStatusEnum::SHIPPED->value, $order->products[1]->shipping_status->value);

    }


    public function test_order_buyer_hidden()
    {
        // 已支付的订单
        $order = $this->test_order_paid();
        /**
         * @var Order $order
         */
        $order = $this->service()->buyerHidden($order->id);
        $order->refresh();
        $this->assertEquals(true, $order->is_buyer_delete, '买家删除状态');
    }

    public function test_order_seller_hidden()
    {
        // 已支付的订单
        $order = $this->test_order_paid();
        /**
         * @var Order $order
         */
        $order = $this->service()->sellerHidden($order->id);
        $order->refresh();
        $this->assertEquals(true, $order->is_seller_delete, '卖家删除状态');
    }


    public function test_order_buyer_remarks()
    {
        // 已支付的订单
        $order = $this->test_order_paid();

        $remarks = fake()->text();
        $data    = OrderRemarksData::from([ 'remarks' => $remarks ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->buyerRemarks($order->id, $data);
        $order->refresh();
        $this->assertEquals($remarks, $order->info->buyer_remarks, '订单备注');

        $remarks2 = fake()->text();
        $data     = OrderRemarksData::from([ 'remarks' => $remarks2, 'is_append' => 1 ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->buyerRemarks($order->id, $data);
        $order->refresh();
        $this->assertStringContainsString($remarks, $order->info->buyer_remarks, '订单备注');
        $this->assertStringContainsString($remarks2, $order->info->buyer_remarks, '订单备注');
    }


    public function test_order_seller_remarks()
    {
        // 已支付的订单
        $order = $this->test_order_paid();

        $remarks = fake()->text();
        $data    = OrderRemarksData::from([ 'remarks' => $remarks ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->sellerRemarks($order->id, $data);
        $order->refresh();
        $this->assertEquals($remarks, $order->info->seller_remarks, '订单备注');

        $remarks2 = fake()->text();
        $data     = OrderRemarksData::from([ 'remarks' => $remarks2, 'is_append' => 1 ]);
        /**
         * @var Order $order
         */
        $order = $this->service()->sellerRemarks($order->id, $data);
        $order->refresh();
        $this->assertStringContainsString($remarks, $order->info->seller_remarks, '订单备注');
        $this->assertStringContainsString($remarks2, $order->info->seller_remarks, '订单备注');
    }


    public function test_order_seller_custom_status()
    {
        // 已支付的订单
        $data  = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order = $this->test_order_paid($data);


        $data = OrderSellerCustomStatusData::from([
                                                      'seller_custom_status' => fake()->word,
                                                  ]);

        /**
         * @var Order $order
         */
        $order = $this->service()->sellerCustomStatus($order->id, $data);

        $this->assertEquals($data->sellerCustomStatus, $order->seller_custom_status, '卖家自定义状态');
    }


    // |-------------------------------------------------------
    // | 测试对订单商品的操作
    // |-------------------------------------------------------

    public function test_order_product_progress()
    {
        // 已支付的订单
        $data  = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order = $this->test_order_paid($data);


        $data = OrderProductProgressData::from([ 'progress'       =>
                                                     fake()->numberBetween(1, 100)
                                                 ,
                                                 'progress_total' => 100
                                               ]);
        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productProgress($order->products[0]->id, $data);
        $orderProduct->refresh();
        $this->assertEquals($data->progress, $orderProduct->progress, '进度');
        $this->assertEquals($data->progressTotal, $orderProduct->progress_total, '总进度');

        $data2 = OrderProductProgressData::from([ 'is_append' => 1, 'progress' => fake()->numberBetween(1, 100), ]);
        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productProgress($order->products[0]->id, $data2);
        $orderProduct->refresh();

        $this->assertEquals($data->progress + $data2->progress, $orderProduct->progress, '进度');
        $this->assertEquals($data->progressTotal, $orderProduct->progress_total, '总进度');
    }


    public function test_order_product_seller_remarks()
    {
        // 已支付的订单
        $data  = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order = $this->test_order_paid($data);

        $orderProductId = $order->products[0]->id;
        $remarks        = fake()->text();
        $data           = OrderRemarksData::from([ 'remarks' => $remarks ]);


        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productSellerRemarks($orderProductId, $data);
        $order->refresh();
        $this->assertEquals($remarks, $orderProduct->info->seller_remarks, '订单备注');

        $remarks2 = fake()->text();
        $data     = OrderRemarksData::from([ 'remarks' => $remarks2, 'is_append' => 1 ]);
        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productSellerRemarks($orderProductId, $data);
        $orderProduct->refresh();
        $this->assertStringContainsString($remarks, $orderProduct->info->seller_remarks, '订单备注');
        $this->assertStringContainsString($remarks2, $orderProduct->info->seller_remarks, '订单备注');
    }

    public function test_order_product_buyer_remarks()
    {
        // 已支付的订单
        $data  = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order = $this->test_order_paid($data);

        $orderProductId = $order->products[0]->id;
        $remarks        = fake()->text();
        $data           = OrderRemarksData::from([ 'remarks' => $remarks ]);


        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productBuyerRemarks($orderProductId, $data);
        $order->refresh();
        $this->assertEquals($remarks, $orderProduct->info->buyer_remarks, '订单备注');

        $remarks2 = fake()->text();
        $data     = OrderRemarksData::from([ 'remarks' => $remarks2, 'is_append' => 1 ]);
        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productBuyerRemarks($orderProductId, $data);
        $orderProduct->refresh();
        $this->assertStringContainsString($remarks, $orderProduct->info->buyer_remarks, '订单备注');
        $this->assertStringContainsString($remarks2, $orderProduct->info->buyer_remarks, '订单备注');
    }


    public function test_order_product_seller_custom_status()
    {
        // 已支付的订单
        $data  = [
            'order'   => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
            'product' => [
                'shipping_type' => ShippingTypeEnum::VIRTUAL,
            ],
        ];
        $order = $this->test_order_paid($data);


        $data = OrderSellerCustomStatusData::from([
                                                      'seller_custom_status' => fake()->word,
                                                  ]);

        /**
         * @var OrderProduct $orderProduct
         */
        $orderProduct = $this->service()->productSellerCustomStatus($order->products[0]->id, $data);

        $this->assertEquals($data->sellerCustomStatus, $orderProduct->seller_custom_status, '卖家自定义状态');
    }
}
