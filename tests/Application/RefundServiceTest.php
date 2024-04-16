<?php

namespace RedJasmine\Order\Tests\Application;

use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeReturnGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCancelCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectReturnGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReturnGoodsCommand;
use RedJasmine\Order\Domain\Enums\Logistics\LogisticsShipperEnum;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\OrderRefund;

class RefundServiceTest extends OrderServiceTest
{

    // 售中阶段

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


    public function test_refund_reject() : OrderRefund
    {
        $refund = $this->test_refund_create();

        $command = RefundRejectCommand::from([ 'rid' => $refund->id, 'reason' => 'reject reason', ]);

        $this->refundService()->reject($command);

        $latestRefund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::SELLER_REJECT_BUYER->value, $latestRefund->refund_status->value);

        $this->assertEquals($command->reason, $latestRefund->reject_reason);


        return $refund;
    }


    public function test_refund_create_after_cancel()
    {

        $refund = $this->test_refund_create();


        $command = RefundCancelCommand::from([ 'rid' => $refund->id ]);


        $this->refundService()->cancel($command);


        $refund = $this->refundRepository()->find($command->rid);


        $this->assertEquals(RefundStatusEnum::REFUND_CLOSED->value, $refund->refund_status->value);

    }


    public function test_refund_reject_after_cancel()
    {

        $refund = $this->test_refund_reject();

        $command = RefundCancelCommand::from([ 'rid' => $refund->id ]);

        $this->refundService()->cancel($command);


        $refund = $this->refundRepository()->find($command->rid);


        $this->assertEquals(RefundStatusEnum::REFUND_CLOSED->value, $refund->refund_status->value);


    }


    // 测试退货退款流程

    public function test_return_goods_refund_create() : OrderRefund
    {

        $this->shippingType = ShippingTypeEnum::EXPRESS;
        $this->productCount = 1;

        // 发货
        $order = $this->test_order_shipping_logistics();

        // 申请退货退款

        $orderProduct = $order->products[0];
        // 申请退款
        $command = RefundCreateCommand::from(
            [
                'id'               => $order->id,
                'order_product_id' => $orderProduct->id,
                'refund_type'      => RefundTypeEnum::RETURN_GOODS_REFUND,
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

        $this->assertEquals(RefundTypeEnum::RETURN_GOODS_REFUND, $refund->refund_type);
        $this->assertEquals(RefundStatusEnum::WAIT_SELLER_AGREE_RETURN->value, $refund->refund_status->value);


        return $refund;


    }


    public function test_return_goods_refund_agree_return_goods()
    {
        $refund = $this->test_return_goods_refund_create();


        $command = RefundAgreeReturnGoodsCommand::from([ 'rid' => $refund->id ]);

        $this->refundService()->agreeReturnGoods($command);

        $refund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::WAIT_BUYER_RETURN_GOODS->value, $refund->refund_status->value);

        return $refund;
    }


    // 退货退款 同意后  退货货物
    public function test_return_goods_refund_return_goods()
    {
        $refund = $this->test_return_goods_refund_agree_return_goods();

        $command = RefundReturnGoodsCommand::from([
                                                      'rid'                  => $refund->id,
                                                      'express_company_code' => 'shunfeng',
                                                      'express_no'           => fake()->numerify('##########')
                                                  ]);
        $this->refundService()->returnGoods($command);


        $refund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::WAIT_SELLER_CONFIRM_GOODS->value, $refund->refund_status->value);

        $logistics = $refund->logistics->first();

        $this->assertEquals($command->expressNo, $logistics->express_no);
        $this->assertEquals($command->expressCompanyCode, $logistics->express_company_code);
        $this->assertEquals(LogisticsShipperEnum::BUYER->value, $logistics->shipper->value);


        return $refund;
    }


    public function test_return_goods_refund_agree()
    {
        $refund = $this->test_return_goods_refund_return_goods();

        $command = RefundAgreeCommand::from([ 'rid' => $refund->id ]);

        $this->refundService()->agree($command);

        $latestRefund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::REFUND_SUCCESS->value, $latestRefund->refund_status->value);
        $this->assertEquals($refund->refund_amount, $latestRefund->refund_amount);


    }


    public function test_return_goods_refund_reject_return_goods()
    {
        $refund = $this->test_return_goods_refund_create();

        $command = RefundRejectReturnGoodsCommand::from([
                                                            'rid'    => $refund->id,
                                                            'reason' => 'reject reason'
                                                        ]);


        $this->refundService()->rejectReturnGoods($command);


        $latestRefund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::SELLER_REJECT_BUYER->value, $latestRefund->refund_status->value);

        $this->assertEquals($command->reason, $latestRefund->reject_reason);


        return $refund;


    }


    public function test_return_goods_refund_reject_return_goods_and_cancel()
    {

        $refund = $this->test_return_goods_refund_reject_return_goods();


        $command = RefundCancelCommand::from([ 'rid' => $refund->id ]);

        $this->refundService()->cancel($command);


        $refund = $this->refundRepository()->find($command->rid);

        $this->assertEquals(RefundStatusEnum::REFUND_CLOSED->value, $refund->refund_status->value);


    }




    public function test_refund_change_goods_create()
    {
        $this->shippingType = ShippingTypeEnum::EXPRESS;
        $this->productCount = 1;

        // 发货
        $order = $this->test_order_shipping_logistics();

        // 申请退货退款

        $orderProduct = $order->products[0];
        // 申请退款
        $command = RefundCreateCommand::from(
            [
                'id'               => $order->id,
                'order_product_id' => $orderProduct->id,
                'refund_type'      => RefundTypeEnum::EXCHANGE_GOODS,
                'refund_amount'    => 0,
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

        $this->assertEquals(RefundTypeEnum::EXCHANGE_GOODS, $refund->refund_type);
        $this->assertEquals(RefundStatusEnum::WAIT_SELLER_AGREE_RETURN->value, $refund->refund_status->value);


        return $refund;
    }
}
