<?php

namespace RedJasmine\Order\Services;

use Exception;
use RedJasmine\Order\Actions\Refunds\RefundAgreeAction;
use RedJasmine\Order\Actions\Refunds\RefundCancelAction;
use RedJasmine\Order\Actions\Refunds\RefundRefuseAction;
use RedJasmine\Order\DataTransferObjects\Refund\OrderProductRefundDTO;
use RedJasmine\Order\DataTransferObjects\Refund\RefundAgreeDTO;
use RedJasmine\Order\DataTransferObjects\Refund\RefundRefuseDTO;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Foundation\Service\Service;
use RedJasmine\Support\Helpers\ID\Snowflake;

/**
 * @see RefundCreateAction::execute()
 * @method static OrderRefund create(int $id, OrderProductRefundDTO $DTO)
 * @see RefundAgreeAction::execute()
 * @method static OrderRefund agree(int $id, RefundAgreeDTO $DTO)
 * @see RefundRefuseAction::execute()
 * @method static OrderRefund refuse(int $id, RefundRefuseDTO $DTO)
 * @see RefundCancelAction::execute()
 * @method static OrderRefund cancel(int $id)
 */
class RefundService extends Service
{
    protected static ?string $actionsConfigKey = 'red-jasmine.order.actions.refund';


    public OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function find($id) : OrderRefund
    {
        return OrderRefund::findOrFail($id);
    }


    public function findLock($id) : OrderRefund
    {
        return OrderRefund::lockForUpdate()->findOrFail($id);
    }


    /**
     * 生成订单ID
     * @return int
     * @throws Exception
     */
    public function buildID() : int
    {
        return Snowflake::getInstance()->nextId();
    }
}
