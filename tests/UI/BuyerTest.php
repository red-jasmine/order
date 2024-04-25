<?php

namespace RedJasmine\Order\Tests\UI;

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


    public function test_ordes_index()
    {

        $this->actingAs(User::make(467823165));
        $response = $this->getJson('api/buyer/order/orders');
        $this->assertEquals(200, $response->status());

    }

}
