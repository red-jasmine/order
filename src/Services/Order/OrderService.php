<?php

namespace RedJasmine\Order\Services\Order;


use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Foundation\Service\ResourceService;

class OrderService extends ResourceService
{


    protected static string $modelClass = Order::class;

    protected static ?string $serviceConfigKey = 'red-jasmine.order.services.order';


    protected array $actions = [
        'create'                    => Actions\OrderCreateAction::class,
        'cancel'                    => Actions\OrderCancelAction::class,
        'paid'                      => Actions\OrderPaidAction::class,
        'logisticsShipping'         => Actions\Shipping\OrderLogisticsShippingAction::class,
        'cardKeyShipping'           => Actions\Shipping\OrderCardKeyShippingAction::class,
        'virtualShipping'           => Actions\Shipping\OrderVirtualShippingAction::class,
        'sellerHidden'              => Actions\Others\OrderSellerHiddenAction::class,
        'buyerHidden'               => Actions\Others\OrderBuyerHiddenAction::class,
        'buyerRemarks'              => Actions\Others\OrderBuyerRemarksAction::class,
        'sellerRemarks'             => Actions\Others\OrderSellerRemarksAction::class,
        'sellerCustomStatus'        => Actions\Others\OrderSellerCustomStatusAction::class,
        'productSellerCustomStatus' => Actions\Products\OrderProductSellerCustomStatusAction::class,
        'productProgress'           => Actions\Products\OrderProductProgressAction::class,
        'productSellerRemarks'      => Actions\Products\OrderProductSellerRemarksAction::class,
        'productBuyerRemarks'       => Actions\Products\OrderProductBuyerRemarksAction::class,
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
