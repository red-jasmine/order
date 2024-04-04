<?php

namespace RedJasmine\Order\Tests\Feature;


use RedJasmine\Order\Domains\Order\Application\Services\OrderService;
use RedJasmine\Order\Domains\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingTypeEnum;
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
                'id'   => 1,
            ],
            'seller'         => [
                'type' => 'buyer',
                'id'   => 1,
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


    public function test_create_for_array()
    {
        $orderDataArray               = $this->fakeOrderArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderDataArray['products'][] = $this->fakeProductArray();
        $orderCreateCommand           = OrderCreateCommand::from($orderDataArray);

        $resultDataData = $this->service()->create($orderCreateCommand);
        dd($orderCreateCommand);
        return $resultDataData;
    }

}
