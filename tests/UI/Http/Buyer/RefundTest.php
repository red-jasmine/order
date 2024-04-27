<?php

namespace RedJasmine\Order\Tests\UI\Http\Buyer;

use RedJasmine\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeCommand;
use RedJasmine\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Enums\RefundTypeEnum;
use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;

class RefundTest extends Base
{


    public function test_can_order_refund_create()
    {
        // 1、创建订单
        // 2、发起支付
        // 3、设置支付成功
        // 4、申请退款
        // 5、同意退款

        $this->user();
        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);

        $this->assertEquals(201, $response->status());

        $orderData      = $response->json('data');
        $orderId        = $orderData['id'];
        $products       = $orderData['products'];
        $orderProductId = $products[0]['id'];

        // 2、发起支付

        $payingRequestData = [ 'id' => $orderId ];
        $payingResponse    = $this->postJson(route('order.buyer.orders.paying', [], false), $payingRequestData);
        $this->assertEquals(200, $payingResponse->status());
        $payingResult     = $payingResponse->json('data');
        $order_payment_id = $payingResult['order_payment_id'];
        $this->assertEquals($orderId, $payingResult['id']);


        // 3、完成支付

        $paymentCommand = OrderPaidCommand::from([
                                                     'id'                 => $orderId,
                                                     'order_payment_id'   => $order_payment_id,
                                                     'amount'             => $payingResult['amount'],
                                                     'payment_type'       => 'payment',
                                                     'payment_id'         => fake()->numberBetween(1000000, 999999999),
                                                     'payment_time'       => date('Y-m-d H:i:s'),
                                                     'payment_channel'    => 'alipay',
                                                     'payment_channel_no' => fake()->numerify('pay-########'),
                                                     'payment_method'     => 'alipay',
                                                 ]);


        $paidResult = $this->orderCommandService()->paid($paymentCommand);

        $this->assertTrue($paidResult);


        // 4、申请退款


        $refundCreateRequestData = [
            'id'               => $orderId,
            'order_product_id' => $orderProductId,
            'images'           => [ fake()->imageUrl, fake()->imageUrl, fake()->imageUrl, ],
            'refund_type'      => RefundTypeEnum::REFUND_ONLY->value,
            'refund_amount'    => null,
            'reason'           => '买错了',
            'description'      => '',
            'order_refund_id'  => fake()->numerify('out-refund-id-########'),

        ];

        $refundCreateResponse = $this->postJson(route('order.buyer.refunds.store', [], false), $refundCreateRequestData);

        $this->assertEquals(200, $refundCreateResponse->status());

        $refundCreateResponseData = $refundCreateResponse->json('data');

        $rid = $refundCreateResponseData['rid'];


        // 5、同意退款

        $agreeRefundCommand = RefundAgreeCommand::from([ 'rid' => $rid ]);
        $this->refundCommandService()->agree($agreeRefundCommand);


        // 6、查询退款详情

        $showRequestData = [ 'refund' => $rid ];
        $showResponse    = $this->getJson(route('order.buyer.refunds.show', $showRequestData, false) );

        $this->assertEquals(200, $showResponse->status());

        $showResponseData = $showResponse->json('data');


        $this->assertEquals(RefundStatusEnum::REFUND_SUCCESS->value,$showResponseData['refund_status']);

        dd($showResponseData);

        dd($refundCreateResponseData);

    }

}
