<?php

namespace RedJasmine\Order\Services;

use Illuminate\Support\Collection;
use RedJasmine\Order\Actions\OrderCreateAction;
use RedJasmine\Order\Actions\OrderPayingAction;
use RedJasmine\Order\Actions\OrderQueryAction;
use RedJasmine\Order\Models\Order;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Foundation\Service\Service;


/**
 * @property OrderPayingAction $paying
 * @property OrderCreateAction $create
 * @method static Order create(UserInterface $seller, UserInterface $buyer, array $orderParameters, Collection $products)
 * @method static Order paying(int $id)
 */
class OrderService extends Service
{

    protected static ?string $actionsConfigKey = 'red-jasmine.order.actions';

    /**
     * @param int $id
     *
     * @return Order
     */
    public function find(int $id) : Order
    {
        return Order::findOrFail($id);
    }

    public function findLock(int $id) : Order
    {
        return Order::lockForUpdate()->findOrFail($id);
    }

    public function queries() : OrderQueryAction
    {
        return app(OrderQueryAction::class)->setService($this);
    }


}