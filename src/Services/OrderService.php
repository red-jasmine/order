<?php

namespace RedJasmine\Order\Services;

use Illuminate\Support\Collection;
use RedJasmine\Order\Actions\OrderCancelAction;
use RedJasmine\Order\Actions\OrderCreateAction;
use RedJasmine\Order\Actions\OrderPaidAction;
use RedJasmine\Order\Actions\OrderPayingAction;
use RedJasmine\Order\Actions\OrderQueryAction;
use RedJasmine\Order\Actions\Shipping\OrderCardKeyShippingAction;
use RedJasmine\Order\Actions\Shipping\OrderLogisticsShippingAction;
use RedJasmine\Order\Actions\Shipping\OrderVirtualShippingAction;
use RedJasmine\Order\DataTransferObjects\OrderPaidInfoDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderCardKeyShippingDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderLogisticsShippingDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderShippingDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Foundation\Service\Service;


/**
 * @property OrderPayingAction $paying
 * @property OrderCreateAction $create
 * @method static Order create(UserInterface $seller, UserInterface $buyer, array $orderParameters, Collection $products)
 * @see OrderCancelAction::execute()
 * @method static Order cancel(int $id)
 * @see OrderPayingAction::execute()
 * @method static Order paying(int $id)
 * @see OrderPaidAction::execute()
 * @method static Order paid(int $id, ?OrderPaidInfoDTO $orderPaidInfoDTO = null)
 * @see OrderVirtualShippingAction::execute()
 * @method static Order virtualShipping(int $id, OrderShippingDTO $orderShippingDTO)
 * @see OrderLogisticsShippingAction::execute()
 * @method static Order logisticsShipping(int $id, OrderLogisticsShippingDTO $orderShippingDTO)
 * @see OrderCardKeyShippingAction::execute()
 * @method static Order cardKeyShipping(int $id, OrderCardKeyShippingDTO $orderShippingDTO)
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

    public function findOrderProduct(int $id) : OrderProduct
    {
        return OrderProduct::findOrFail($id);
    }

    public function findOrderProductLock(int $id) : OrderProduct
    {
        return OrderProduct::lockForUpdate()->findOrFail($id);
    }


    public function queries() : OrderQueryAction
    {
        return app(OrderQueryAction::class)->setService($this);
    }


}
