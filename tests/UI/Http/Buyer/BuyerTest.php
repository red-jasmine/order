<?php

namespace RedJasmine\Order\Tests\UI\Http\Buyer;


use RedJasmine\Order\Tests\Fixtures\Orders\OrderFake;
use RedJasmine\Order\Tests\Fixtures\Users\User;
use RedJasmine\Order\Tests\TestCase;
use RedJasmine\Order\UI\Http\Buyer\Api\OrderBuyerApiRoute;
use RedJasmine\Support\Contracts\BelongsToOwnerInterface;

class BuyerTest extends TestCase
{


    /**
     * Define routes setup.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        // Define routes.
        $router->group([
                           'prefix' => 'api/buyer'
                       ], function () {
            OrderBuyerApiRoute::route();
        });


    }

    protected function user() : User
    {
        $user = User::make(1);
        $this->actingAs($user);
        return $user;
    }

    protected function owner()
    {
        $user = $this->user();
        if ($user instanceof BelongsToOwnerInterface) {
            return $user->owner();
        }
        return $user;

    }


    public function test_can_create_order()
    {
        $this->user();

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);

        $this->assertEquals(201, $response->status());


        $orderData = $response->json('data');

        $this->assertEquals($this->owner()->getType(), $orderData['buyer_type']);
        $this->assertEquals($this->owner()->getID(), $orderData['buyer_id']);

        $this->assertEquals($this->user()->getType(), $orderData['creator_type']);
        $this->assertEquals($this->user()->getID(), $orderData['creator_id']);

    }


    public function test_create_after_can_index_and_show() : void
    {
        $this->user();

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
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


    public function test_can_order_cancel() : void
    {
        $this->user();

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
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
        $this->user();

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
        unset($requestData['buyer']);
        $response = $this->postJson(route('order.buyer.orders.store', [], false), $requestData);
        $this->assertEquals(201, $response->status());

        $orderData = $response->json('data');
        $orderId   = $orderData['id'];


        $cancelResponse = $this->deleteJson(route('order.buyer.orders.destroy', [ 'order' => $orderId ], false));
        $this->assertEquals(200, $cancelResponse->status());

    }

    public function test_can_order_paying() : void
    {
        $this->user();

        // 创建订单
        $orderFake   = new OrderFake();
        $requestData = $orderFake->fake();
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


}
