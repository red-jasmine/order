<?php

namespace RedJasmine\Order\Application\Services\Payments;

use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentFailCommand;
use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentFailCommandHandler;
use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentPaidCommand;
use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentPaidCommandHandler;
use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentPayingCommand;
use RedJasmine\Order\Application\Services\Payments\Commands\OrderPaymentPayingCommandHandler;
use RedJasmine\Order\Domain\Models\OrderPayment;
use RedJasmine\Support\Application\ApplicationCommandService;

/**
 * @see OrderPaymentPayingCommandHandler::handle()
 * @method void paying(OrderPaymentPayingCommand $command)
 * @see OrderPaymentPaidCommandHandler::handle()
 * @method void paid(OrderPaymentPaidCommand $command)
 * @see OrderPaymentFailCommandHandler::handle()
 * @method void fail(OrderPaymentFailCommand $command)
 */
class OrderPaymentCommandService extends ApplicationCommandService
{

    /**
     * 钩子前缀
     * @var string
     */
    public static string $hookNamePrefix = 'order.application.order-payment.command';

    protected static string $modelClass = OrderPayment::class;


    protected static $macros = [
        'paying' => OrderPaymentPayingCommandHandler::class,
        'paid'   => OrderPaymentPaidCommandHandler::class,
        'fail'   => OrderPaymentFailCommandHandler::class,


    ];
}
