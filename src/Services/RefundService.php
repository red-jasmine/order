<?php

namespace RedJasmine\Order\Services;

use Exception;
use RedJasmine\Order\Actions\Refunds\RefundAgreeAction;
use RedJasmine\Order\Actions\Refunds\RefundAgreeReturnGoodsAction;
use RedJasmine\Order\Actions\Refunds\RefundCancelAction;
use RedJasmine\Order\Actions\Refunds\RefundRefuseAction;
use RedJasmine\Order\Actions\Refunds\RefundRefuseReturnGoodsAction;
use RedJasmine\Order\Actions\Refunds\RefundReturnGoodsAction;
use RedJasmine\Order\Actions\Refunds\RefundSellerReturnGoodsAction;
use RedJasmine\Order\DataTransferObjects\Refund\OrderProductRefundDTO;
use RedJasmine\Order\DataTransferObjects\Refund\RefundAgreeDTO;
use RedJasmine\Order\DataTransferObjects\Refund\RefundRefuseDTO;
use RedJasmine\Order\DataTransferObjects\Refund\RefundReturnGoodsDTO;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Foundation\Service\Service;
use RedJasmine\Support\Helpers\ID\Snowflake;

/**
 * @see RefundCreateAction::execute()
 * @method  OrderRefund create(int $id, OrderProductRefundDTO $DTO)
 * @see RefundAgreeAction::execute()
 * @method  OrderRefund agree(int $id, RefundAgreeDTO $DTO)
 * @see RefundRefuseAction::execute()
 * @method  OrderRefund refuse(int $id, RefundRefuseDTO $DTO)
 * @see RefundCancelAction::execute()
 * @method  OrderRefund cancel(int $id)
 * @see RefundAgreeReturnGoodsAction::execute()
 * @method  OrderRefund agreeReturnGoods(int $id)
 * @see RefundReturnGoodsAction::execute()
 * @method  OrderRefund returnGoods(int $id, RefundReturnGoodsDTO $DTO)
 * @see RefundRefuseReturnGoodsAction::execute()
 * @see RefundSellerReturnGoodsAction::execute()
 * @method  OrderRefund refuseReturnGoods(int $id, RefundRefuseDTO $DTO)
 * @see RefundSellerReturnGoodsAction::execute()
 * @method  OrderRefund sellerReturnGoods(int $id, RefundReturnGoodsDTO $DTO)
 */
class RefundService extends Service
{
    protected static ?string $actionsConfigKey = 'red-jasmine.order.actions.refund';


    public OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        parent::__construct();
    }


    public function find($id) : OrderRefund
    {
        return OrderRefund::findOrFail($id);
    }


    public function findLock($id) : OrderRefund
    {
        return OrderRefund::lockForUpdate()->findOrFail($id);
    }



}
