<?php

namespace RedJasmine\Order\Tests\Application;

use RedJasmine\Order\Application\Data\OrderData;
use RedJasmine\Order\Application\Mappers\OrderMapper;
use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Order\Infrastructure\Repositories\Eloquent\RefundRepository;
use RedJasmine\Order\Tests\Fixtures\Users\User;
use RedJasmine\Order\Tests\TestCase;

class OrderBase extends TestCase
{

    //ShippingTypeEnum::EXPRESS->value, ShippingTypeEnum::VIRTUAL->value, ShippingTypeEnum::CDK->value

    protected OrderTypeEnum $orderType = OrderTypeEnum::SOP;

    /**
     * 发货类型
     * @var ShippingTypeEnum
     */
    protected ShippingTypeEnum $shippingType = ShippingTypeEnum::VIRTUAL;
    // 商品数量
    protected int $productCount = 3;

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
        $user = fake()->randomElement([ User::make(1), User::make(2), User::make(3) ]);


        $fake = [
            'buyer'          => [
                'type'     => $user->getType(),
                'id'       => $user->getId(),
                'nickname' => fake()->name(),
            ],
            'seller'         => [
                'type'     => 'seller',
                'id'       => fake()->numberBetween(1000000, 999999999),
                'nickname' => fake()->name()
            ],
            'title'          => fake()->text(),
            'order_type'     => $this->orderType->value,
            'shipping_type'  => $this->shippingType->value,
            'source_type'    => fake()->randomElement([ 'product', 'activity' ]),
            'source_id'      => fake()->numerify('out-order-id-########'),
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
            'client_version'  => fake()->randomNumber(),
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
            'shipping_type'          => $this->shippingType->value,
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
            'num'                    => fake()->numberBetween(2, 5),
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

    protected function orderService() : OrderCommandService
    {
        return app(OrderCommandService::class)->setOperator($this->getOperator());
    }

    protected function refundService() : RefundCommandService
    {
        return app(RefundCommandService::class)->setOperator($this->getOperator());
    }

    protected function orderRepository() : OrderRepositoryInterface
    {
        return app(OrderRepositoryInterface::class);
    }

    protected function refundRepository() : RefundRepository
    {
        return app(RefundRepository::class);
    }


    public function test_order_create()
    {
        $orderDataArray = $this->fakeOrderArray();
        for ($i = 1; $i <= $this->productCount; $i++) {
            $orderDataArray['products'][] = $this->fakeProductArray();
        }
        $orderCreateCommand = OrderCreateCommand::from($orderDataArray);

        $order = $this->orderService()->create($orderCreateCommand);

        $this->assertInstanceOf(Order::class, $order);
        // TODO
        return app(OrderMapper::class)->fromModel($order);
    }


    public function test_order_paying() : array
    {

        $orderData = $this->test_order_create();


        $command = OrderPayingCommand::from([
                                                'id'          => $orderData->id,
                                                'amount'      => $orderData->payableAmount,
                                                'amount_type' => 'full'
                                            ]);


        $orderPaymentID = $this->orderService()->paying($command);

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


    public function test_order_paid() : Order
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


        $this->orderService()->paid($command);

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


}
