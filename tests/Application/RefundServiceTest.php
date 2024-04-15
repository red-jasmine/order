<?php

namespace RedJasmine\Order\Tests\Application;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Models\OrderRefund;

class RefundServiceTest extends OrderBaseTest
{


    public function test_refund_create() : OrderRefund
    {
        // 创建订单支付
        $order = $this->test_order_paid();

        $orderProduct = $order->products[0];
        // 申请退款
        $command = RefundCreateCommand::from(
            [
                'id'               => $order->id,
                'order_product_id' => $orderProduct->id,
                'refund_type'      => RefundTypeEnum::REFUND_ONLY,
                'refund_amount'    => $orderProduct->payment_amount,
                'out_refund_id'    => fake()->numerify('out-sku-id-########'),
                'description'      => 'test refund',
                'reason'           => 'test reason',
                'images'           => [ fake()->imageUrl, fake()->imageUrl, fake()->imageUrl, ],
            ]
        );

        $refundId = $this->refundService()->create($command);

        $refund = $this->refundRepository()->find($refundId);


        $this->assertEquals($order->id, $refund->order_id);
        $this->assertEquals($orderProduct->id, $refund->order_product_id);

        $this->assertEquals($command->refundType, $refund->refund_type);
        $this->assertEquals(RefundStatusEnum::WAIT_SELLER_AGREE->value, $refund->refund_status->value);


        return $refund;
    }


    public function test_refund_agree()
    {
        $refund = $this->test_refund_create();

        $command = RefundAgreeCommand::from([
                                                'rid' => $refund->id,
                                            ]);
        $this->refundService()->agree($command);

        $latestRefund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::REFUND_SUCCESS->value, $latestRefund->refund_status->value);
        $this->assertEquals($refund->refund_amount, $latestRefund->refund_amount);

    }


    public function test_refund_reject()
    {
        $refund = $this->test_refund_create();

        $command = RefundRejectCommand::from([ 'rid' => $refund->id, 'reason' => 'reject reason', ]);

        $this->refundService()->reject($command);

        $latestRefund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::SELLER_REJECT_BUYER->value, $latestRefund->refund_status->value);

        $this->assertEquals($command->reason, $latestRefund->reject_reason);
    }


}
