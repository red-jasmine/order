<?php

namespace RedJasmine\Order\Tests\UI\Http\Buyer;


use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;
use RedJasmine\Order\Tests\Fixtures\Users\User;
use RedJasmine\Order\Tests\TestCase;
use RedJasmine\Order\UI\Http\Buyer\Api\OrderBuyerApiRoute;
use RedJasmine\Support\Contracts\BelongsToOwnerInterface;

class OrderTest extends Base
{

    protected function setUp() : void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user();
    }


    /**
     *  能创建订单
     * 前提条件:
     * 步骤：
     *  1、创建订单成功
     *  2、
     *  3、
     * 预期结果:
     *  1、能查询出订单
     *  2、订单卖家是当前用户
     * @return void
     */
    public function test_can_create_order() : void
    {

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->order();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);

        $this->assertEquals(201, $response->status());


        $orderData = $response->json('data');

        $this->assertEquals($this->owner()->getType(), $orderData['buyer_type']);
        $this->assertEquals($this->owner()->getID(), $orderData['buyer_id']);

        $this->assertEquals($this->user()->getType(), $orderData['creator_type']);
        $this->assertEquals($this->user()->getID(), $orderData['creator_id']);

    }


    /**
     *  创建订单能后 列表 和 详情能查询到
     * 前提条件: 准备数据
     * 步骤：
     *  1、创建订单
     *  2、查询列表
     *  3、查询详情
     * 预期结果:
     *  1、列表存在订单ID
     *  2、详情查询成功
     * @return void
     */
    public function test_create_after_can_index_and_show() : void
    {


        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->order();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);

        $this->assertEquals(201, $response->status());

        $orderData = $response->json('data');
        $orderId   = $orderData['id'];

        // 能列表查看
        $includeParameterName = config('query-builder.parameters.include', 'include');
        $query                = [ $includeParameterName => 'info,products,products.info,address,payments,logistics', ];
        $indexResponse        = $this->getJson(route('order.buyer.orders.index', $query, false));
        $this->assertEquals(200, $indexResponse->status());


        $indexResult = $indexResponse->json();
        $this->assertEquals($orderId, $indexResult['data'][0]['id']);


        // 能单独查询
        $includeParameterName = config('query-builder.parameters.include', 'include');
        $showQuery            = [ 'order' => $orderId, $includeParameterName => 'info,products,products.info,address,payments,logistics', ];

        $showResponse = $this->getJson(route('order.buyer.orders.show', $showQuery, false));

        $showResult = $showResponse->json();
        $this->assertEquals($orderId, $showResult['data']['id']);
    }


    /**
     *  创建订单后能取消
     * 前提条件:准备订单数据
     * 步骤：
     *  1、创建订单
     *  2、取消订单接口
     * 预期结果:
     *  1、接口调用成功
     * @return void
     */
    public function test_can_order_cancel() : void
    {


        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->order();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);
        $this->assertEquals(201, $response->status());

        $orderData = $response->json('data');
        $orderId   = $orderData['id'];

        $requestData    = [
            'id'            => $orderId,
            'cancel_reason' => '我不想要了'
        ];
        $cancelResponse = $this->postJson(route('order.buyer.orders.cancel', [], false), $requestData);
        $this->assertEquals(200, $cancelResponse->status());

    }

    public function test_can_order_delete() : void
    {


        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->order();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);
        $this->assertEquals(201, $response->status());

        $orderData = $response->json('data');
        $orderId   = $orderData['id'];


        $cancelResponse = $this->deleteJson(route('order.buyer.orders.destroy', [ 'order' => $orderId ], false));
        $this->assertEquals(200, $cancelResponse->status());

    }

    /**
     *  订单能发起支付
     * 前提条件: 准备订单数据
     * 步骤：
     *  1、创建订单
     *  2、发起支付
     *  3、
     * 预期结果:
     *  1、能获取订单支付单ID
     *  2、
     * @return void
     */
    public function test_can_order_paying() : void
    {


        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->order();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);
        $this->assertEquals(201, $response->status());

        $orderData = $response->json('data');
        $orderId   = $orderData['id'];

        $payingRequestData = [ 'id' => $orderId ];
        $payingResponse    = $this->postJson(route('order.buyer.orders.paying', [], false), $payingRequestData);
        $this->assertEquals(200, $payingResponse->status());
        $payingResult = $payingResponse->json('data');

        $this->assertEquals($orderId, $payingResult['id']);
    }


    /**
     *  能进行订单确认
     * 前提条件: 准备订单数据
     * 步骤：
     *  1、创建订单
     *  2、发起支付
     *  3、支付成功
     *  4、卖家发货
     *  5、发起确认
     * 预期结果:
     *  1、接口确认成功
     *  2、订单已确认
     * @return void
     */
    public function test_can_order_confirm() : void
    {
        // TODO

        $this->assertTrue(true);


    }
}
