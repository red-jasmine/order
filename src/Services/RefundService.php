<?php

namespace RedJasmine\Order\Services;

use Exception;
use RedJasmine\Order\DataTransferObjects\Refund\OrderProductRefundDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Foundation\Service\Service;
use RedJasmine\Support\Helpers\ID\Snowflake;

/**
 * @see RefundCreateAction::execute()
 * @method static Order create(int $id, OrderProductRefundDTO $DTO)
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
