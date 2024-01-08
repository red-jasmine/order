<?php

namespace RedJasmine\Order;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Traits\Macroable;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;
use RedJasmine\Order\Services\Orders\OrderCreatorService;
use RedJasmine\Order\Services\Orders\Actions\OrderPayAction;
use RedJasmine\Order\Services\Orders\OrderQueryService;
use RedJasmine\Support\Traits\Services\WithUserService;

/**
 * @method static OrderPayAction pay()
 */
class OrderService
{

    use WithUserService;

    use Macroable {
        __call as macroCall;
    }


    /**
     * Execute a method against a new pending request instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($this->hacAction($method)) {
            return $this->callAction($method, $parameters);

        }
    }

    protected static array $actions = [];


    protected static function action($name, $action) : void
    {
        self::$actions[$name] = $action;
    }

    public static function hacAction($method) : bool
    {
        return isset(self::$actions[$method]);
    }


    public function actions()
    {
        return Config::get('red-jasmine.order.actions', []);
    }


    /**
     * @param string $name
     * @param        $parameters
     *
     * @return AbstractOrderAction
     */
    public function callAction(string $name, $parameters) : AbstractOrderAction
    {
        $action = self::$actions[$name];

        if ($action instanceof Closure) {
            $action = $action->bindTo($this, static::class);
            return $action(...$parameters);
        }
        /**
         * @var $action AbstractOrderAction
         */
        $action = app($action, $parameters);
        $action->setService($this);
        return $action;
    }


    /**
     * @param int $id
     *
     * @return Order
     */
    public function find(int $id) : Order
    {
        return Order::findOrFail($id);
    }

    public function queries() : OrderQueryService
    {
        return app(OrderQueryService::class)->setService($this);
    }

    public function creator() : OrderCreatorService
    {
        return app(OrderCreatorService::class)->setService($this);
    }


}
