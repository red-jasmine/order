<?php

namespace RedJasmine\Order\Tests\Fixtures\Orders;

use RedJasmine\Order\Domain\Enums\OrderProductTypeEnum;
use RedJasmine\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Tests\Fixtures\Users\User;

class OrderFake
{


    public OrderTypeEnum $orderType = OrderTypeEnum::SOP;

    /**
     * 发货类型
     * @var ShippingTypeEnum
     */
    public ShippingTypeEnum $shippingType = ShippingTypeEnum::VIRTUAL;
    // 商品数量
    protected int $productCount = 3;


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

    public function fakeProductArray(array $product = []) : array
    {
        $fake = [
            'shipping_type'          => $this->shippingType->value,
            'order_product_type'     => fake()->randomElement([OrderProductTypeEnum::GOODS->value]),
            'title'                  => fake()->sentence(),
            'sku_name'               => fake()->words(1, true),
            'image'                  => fake()->imageUrl,
            'product_type'           => 'product',
            'product_id'             => fake()->numberBetween(1000000, 999999999),
            'sku_id'                 => fake()->numberBetween(1000000, 999999999),
            'category_id'            => 0,
            'seller_category_id'     => 0,
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


    public function fake(array $order = []) : array
    {
        $orderDataArray = $this->fakeOrderArray($order);
        for ($i = 1; $i <= $this->productCount; $i++) {
            $orderDataArray['products'][] = $this->fakeProductArray();
        }
        return $orderDataArray;
    }

}
