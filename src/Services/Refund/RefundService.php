<?php

namespace RedJasmine\Order\Services\Refund;

use RedJasmine\Order\Models\OrderRefund;
use RedJasmine\Support\Foundation\Service\ResourceService;
use RedJasmine\Order\Services\Refund\Actions;

class RefundService extends ResourceService
{


    protected array $actions = [
        'create' => Actions\RefundCreateAction::class,
    ];

    protected static ?string $serviceConfigKey = 'red-jasmine.order.services.refund';


    protected static string $modelClass = OrderRefund::class;


    public function find($id) : OrderRefund
    {
        return OrderRefund::findOrFail($id);
    }


    public function findLock($id) : OrderRefund
    {
        return OrderRefund::lockForUpdate()->findOrFail($id);
    }


}
