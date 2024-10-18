<?php

namespace RedJasmine\Order\Tests\Fixtures\Orders;

use RedJasmine\Ecommerce\Domain\Models\Enums\ProductTypeEnum;
use RedJasmine\Ecommerce\Domain\Models\Enums\RefundTypeEnum;
use RedJasmine\Ecommerce\Domain\Models\Enums\ShippingTypeEnum;
use RedJasmine\Ecommerce\Domain\Models\ValueObjects\PromiseServices;
use RedJasmine\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderProgressCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand;
use RedJasmine\Order\Domain\Models\Enums\OrderTypeEnum;
use RedJasmine\Order\Tests\Fixtures\Users\User;

class OrderFake
{


    public OrderTypeEnum $orderType = OrderTypeEnum::STANDARD;

    /**
     * 发货类型
     * @var ShippingTypeEnum
     */
    public ShippingTypeEnum $shippingType = ShippingTypeEnum::EXPRESS;
    // 商品数量
    public int $productCount = 3;


    public int $unit = 1;


    public function fakeAddressArray() : array
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

    public function fakeOrderArray(array $order = []) : array
    {
        $user = User::make(1);

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
            'source_type'    => fake()->randomElement([ 'product', 'activity' ]),
            'source_id'      => fake()->numerify('out-order-id-########'),
            'outer_order_id' => fake()->numerify('out-order-id-########'),

            'channel' => [
                'type' => fake()->randomElement([ 'channel', 'promoter' ]),
                'id'   => fake()->randomNumber(5, true),
            ],

            'store' => [
                'type' => fake()->randomElement([ 'self', 'franchise' ]),
                'id'   => fake()->randomNumber(5, true),
            ],
            'guide' => [
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

    public function fakeProductArray(array $product = []) : array
    {
        $fake = [
            'shipping_type'          => $this->shippingType->value,
            'order_product_type'     => ProductTypeEnum::GOODS->value,
            'title'                  => fake()->sentence(),
            'sku_name'               => fake()->words(1, true),
            'image'                  => fake()->imageUrl,
            'product_type'           => 'product',
            'product_id'             => fake()->numberBetween(1000000, 999999999),
            'sku_id'                 => fake()->numberBetween(1000000, 999999999),
            'category_id'            => 0,
            'product_group_id'       => 0,
            'outer_id'               => fake()->numerify('out-id-########'),
            'outer_sku_id'           => fake()->numerify('out-sku-id-########'),
            'barcode'                => fake()->ean13(),
            'num'                    => fake()->numberBetween(1, 10),
            'unit'                   => $this->unit,
            'price'                  => fake()->randomFloat(2, 90, 100),
            'cost_price'             => fake()->randomFloat(2, 70, 80),
            'tax_amount'             => fake()->randomFloat(2, 10, 20),
            'discount_amount'        => fake()->randomFloat(2, 5, 20),
            'outer_order_product_id' => fake()->numerify('CODE-########'),
            'promise_services'       => PromiseServices::from([
                                                                  'refund'   => '7day',// 退款
                                                                  // 'refund'    => 'unsupported',// 退款
                                                                  'exchange' => '15day', // 换货
                                                                  'service'  => '3month', // 保修
                                                              ])->toArray(),
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


    public function order(array $order = []) : array
    {
        $orderDataArray = $this->fakeOrderArray($order);
        for ($i = 1; $i <= $this->productCount; $i++) {
            $orderDataArray['products'][] = $this->fakeProductArray();
        }
        return $orderDataArray;
    }


    public function paid(array $merge = []) : OrderPaidCommand
    {

        $data = [
            'id'                  => 1,
            'order_payment_id'    => 1,
            'amount'              => 0,
            'payment_time'        => date('Y-m-d H:i:s'),
            'payment_type'        => 'payment',
            'payment_id'          => fake()->numberBetween(1000000, 999999999),
            'payment_channel'     => fake()->randomNumber([ 'alipay', 'wechat' ]),
            'payment_channel_no'  => fake()->numerify('out-sku-id-########'),
            'payment_method_type' => fake()->randomElement([ 'h5', 'applets', 'ios-app', 'android' ]),
        ];

        $data = array_merge($data, $merge);
        return OrderPaidCommand::from($data);
    }


    public function shippingLogistics(array $merge = []) : OrderShippingLogisticsCommand
    {
        $data = [
            'id'                   => 1,
            'is_split'             => false,
            'order_products'       => null,
            'express_company_code' => fake()->randomElement([ 'shunfeng', 'yuantong', ]),
            'express_no'           => fake()->numerify('##########'),
        ];

        $data = array_merge($data, $merge);

        return OrderShippingLogisticsCommand::from($data);
    }


    public function shippingCardKey(array $merge) : OrderShippingCardKeyCommand
    {
        $data = [
            'id'               => 1,
            'order_product_id' => 0,
            'content'          => fake()->text(),
            'extends'          => [],
        ];

        $data = array_merge($data, $merge);
        return OrderShippingCardKeyCommand::from($data);
    }

    public function shippingVirtual(array $merge) : OrderShippingVirtualCommand
    {
        $data = [
            'id'               => 0,
            'order_product_id' => 0,
            'is_finished'      => true,
        ];

        $data = array_merge($data, $merge);
        return OrderShippingVirtualCommand::from($data);
    }


    public function progress(array $merge = []) : OrderProgressCommand
    {
        $data = [
            'id'               => 0,
            'order_product_id' => 0,
            'progress'         => 1,
            'is_absolute'      => true,
            'is_allow_less'    => false,
        ];

        $data = array_merge($data, $merge);
        return OrderProgressCommand::from($data);
    }


    public function createRefund(array $merge = []) : RefundCreateCommand
    {
        $data = [
            'id'               => 0,
            'order_product_id' => 0,
            'images'           => [ fake()->imageUrl, fake()->imageUrl ],
            'refund_type'      => RefundTypeEnum::REFUND->value,
            'reason'           => fake()->randomElement([ '不想要了', '拍错了' ]),
            'refund_amount'    => null,
            'description'      => fake()->text,
            'outer_refund_id'  => fake()->numerify('##########'),
        ];

        $data = array_merge($data, $merge);
        return RefundCreateCommand::from($data);
    }
}
