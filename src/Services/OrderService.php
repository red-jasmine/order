<?php

namespace RedJasmine\Order\Services;

use Exception;
use Illuminate\Support\Collection;
use RedJasmine\Order\Actions\OrderCancelAction;
use RedJasmine\Order\Actions\OrderConfirmAction;
use RedJasmine\Order\Actions\OrderCreateAction;
use RedJasmine\Order\Actions\OrderPaidAction;
use RedJasmine\Order\Actions\OrderPayingAction;
use RedJasmine\Order\Actions\OrderQueryAction;
use RedJasmine\Order\Actions\Others\OrderBuyerHiddenAction;
use RedJasmine\Order\Actions\Others\OrderBuyerRemarksAction;
use RedJasmine\Order\Actions\Others\OrderProductProgressAction;
use RedJasmine\Order\Actions\Others\OrderSellerProductRemarksAction;
use RedJasmine\Order\Actions\Others\OrderSellerHiddenAction;
use RedJasmine\Order\Actions\Others\OrderSellerRemarksAction;
use RedJasmine\Order\Actions\Shipping\OrderCardKeyShippingAction;
use RedJasmine\Order\Actions\Shipping\OrderLogisticsShippingAction;
use RedJasmine\Order\Actions\Shipping\OrderVirtualShippingAction;
use RedJasmine\Order\DataTransferObjects\OrderPaidInfoDTO;
use RedJasmine\Order\DataTransferObjects\OrderSplitProductDTO;
use RedJasmine\Order\DataTransferObjects\Others\OrderProductProgressDTO;
use RedJasmine\Order\DataTransferObjects\Others\OrderRemarksDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderCardKeyShippingDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderLogisticsShippingDTO;
use RedJasmine\Order\DataTransferObjects\Shipping\OrderShippingDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Foundation\Service\ResourceService;
use RedJasmine\Support\Foundation\Service\Service;
use RedJasmine\Support\Helpers\ID\Snowflake;


/**
 * @property OrderPayingAction $paying
 * @property OrderCreateAction $create
 * @method  Order create(UserInterface $seller, UserInterface $buyer, array $orderParameters, Collection $products)
 * @see OrderCancelAction::execute()
 * @method  Order cancel(int $id)
 * @see OrderPayingAction::execute()
 * @method  Order paying(int $id)
 * @see OrderPaidAction::execute()
 * @method  Order paid(int $id, ?OrderPaidInfoDTO $orderPaidInfoDTO = null)
 * @see OrderVirtualShippingAction::execute()
 * @method  Order virtualShipping(int $id, OrderShippingDTO $orderShippingDTO)
 * @see OrderLogisticsShippingAction::execute()
 * @method  Order logisticsShipping(int $id, OrderLogisticsShippingDTO $orderShippingDTO)
 * @see OrderCardKeyShippingAction::execute()
 * @method  Order cardKeyShipping(int $id, OrderCardKeyShippingDTO $orderShippingDTO)
 * @see OrderConfirmAction::execute()
 * @method  Order confirm(int $id, OrderSplitProductDTO $DTO)
 * @see OrderProductProgressAction::execute()
 * @method  OrderProduct productProgress(int $id, OrderProductProgressDTO $DTO)
 * @see OrderSellerHiddenAction::execute()
 * @method  OrderProduct sellerHidden(int $id)
 * @see OrderBuyerHiddenAction::execute()
 * @method  OrderProduct buyerHidden(int $id)
 * @see OrderSellerRemarksAction::execute()
 * @method  Order sellerRemarks(int $id, OrderRemarksDTO $DTO)
 * @see OrderBuyerRemarksAction::execute()
 * @method  Order buyerRemarks(int $id, OrderRemarksDTO $DTO)
 * @see OrderSellerProductRemarksAction::execute()
 * @method  OrderProduct sellerProductRemarks(int $id, OrderRemarksDTO $DTO)
 * @see OrderBuyerProductRemarksAction::execute()
 * @method  OrderProduct buyerProductRemarks(int $id, OrderRemarksDTO $DTO)
 */
class OrderService extends ResourceService
{


    protected static string $modelClass = Order::class;

    protected static ?string $serviceConfigKey = 'red-jasmine.order.services.order';



    protected array $actions = [


    ];

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


    /**
     * 获取当前订单最大退款金额
     *
     * @param OrderProduct $orderProduct
     *
     * @return string
     */
    public function getOrderProductMaxRefundAmount(OrderProduct $orderProduct) : string
    {
        return bcsub($orderProduct->divided_payment_amount, $orderProduct->refund_amount, 2);
    }


}
