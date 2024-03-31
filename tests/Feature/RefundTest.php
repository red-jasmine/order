<?php

namespace RedJasmine\Order\Tests\Feature;

use RedJasmine\Order\Services\Refund\Data\OrderProductRefundData;
use RedJasmine\Order\Services\Refund\Enums\RefundGoodsStatusEnum;
use RedJasmine\Order\Services\Refund\Enums\RefundTypeEnum;
use RedJasmine\Order\Services\Refund\RefundService;
use RedJasmine\Order\Tests\TestCase;

class RefundTest extends TestCase
{

    protected function service() : RefundService
    {
        return app(RefundService::class);
    }

    protected function fakeRefundArray(array $data = []):array
    {
        $fake =  [
            'refund_type'     => fake()->randomElement(RefundTypeEnum::values()),
            'reason'          => fake()->text(10),
            'description'     => fake()->text(100),
            'refund_amount'   => 0,
            'outer_refund_id' => fake()->numerify('##########'),
            'images'          => [ fake()->imageUrl, fake()->imageUrl, fake()->imageUrl, ],
            'good_status'     => fake()->randomElement(RefundGoodsStatusEnum::values()),
            'freight_amount'  => 0,
        ];

        return  array_merge($fake,$data);
    }

    public function test_refund_create_only_money()
    {
        $orderTest = new OrderTest();
        $order     = $orderTest->test_order_paid();
        $data =  OrderProductRefundData::from($this->fakeRefundArray())->toArray();
        $this->service()->create($data);

    }

}
