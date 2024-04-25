<?php

namespace RedJasmine\Order\Tests\UI;

use Illuminate\Support\Facades\Route;
use RedJasmine\Order\Tests\TestCase;
use RedJasmine\Order\Tests\Users\User;
use RedJasmine\Order\UI\Http\Buyer\Api\OrderBuyerApiRoute;

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


    public function test_orders_index()
    {

        $this->actingAs(User::make(1));
        $includeParameterName = config('query-builder.parameters.include', 'include');
        $query                = [ $includeParameterName => 'info,products,products.info,address,payments,logistics', ];
        $response             = $this->getJson(route('order.buyer.orders.index', $query, false));
        $this->assertEquals(200, $response->status());
        // TODO 更多验证

        return $response->json();
    }


    public function test_orders_show():int
    {
        $this->actingAs(User::make(1));
        $orders = $this->test_orders_index();
        $id     = $orders['data'][0]['id'] ?? null;

        $includeParameterName = config('query-builder.parameters.include', 'include');
        $query                = [ 'order' => $id, $includeParameterName => 'info,products,products.info,address,payments,logistics', ];


        $response = $this->getJson(route('order.buyer.orders.show', $query, false));

        $this->assertEquals(200, $response->status());
        // TODO 更多验证

        return $id;


    }

    public function test_order_delete()
    {
        $this->actingAs(User::make(1));
        $id = $this->test_orders_show();

        $query                = [ 'order' => $id, ];

        $response = $this->deleteJson(route('order.buyer.orders.destroy', $query, false));
        $this->assertEquals(200, $response->status());
    }

}
