<?php

namespace RedJasmine\Order\Application\Services;


use RedJasmine\Order\Application\Data\OrderData;
use RedJasmine\Order\Application\Services\Handlers\OrderCancelCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\OrderConfirmCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\OrderCreateCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\OrderPaidCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\OrderPayingCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\OrderProgressCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Others\OrderHiddenCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Others\OrderRemarksCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Others\OrderSellerCustomStatusCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Shipping\OrderShippingCardKeyCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Shipping\OrderShippingLogisticsCommandHandler;
use RedJasmine\Order\Application\Services\Handlers\Shipping\OrderShippingVirtualCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\OrderCancelCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderConfirmCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPaidCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderPayingCommand;
use RedJasmine\Order\Application\UserCases\Commands\OrderProgressCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderHiddenCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderRemarksCommand;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderSellerCustomStatusCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingCardKeyCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingLogisticsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Shipping\OrderShippingVirtualCommand;
use RedJasmine\Order\Domain\Enums\TradePartyEnums;


class OrderService
{

    public function operator()
    {

    }
    // TODO 可扩展替换操作
    // TODO 可获取当前操作人

    public function create(OrderCreateCommand $command) : OrderData
    {
        return app(OrderCreateCommandHandler::class)->execute($command);
    }

    public function paying(OrderPayingCommand $command) : int
    {
        return app(OrderPayingCommandHandler::class)->execute($command);
    }

    public function paid(OrderPaidCommand $command) : bool
    {
        return app(OrderPaidCommandHandler::class)->execute($command);
    }

    public function cancel(OrderCancelCommand $command)
    {
        return app(OrderCancelCommandHandler::class)->execute($command);
    }

    public function shippingLogistics(OrderShippingLogisticsCommand $command)
    {
        return app(OrderShippingLogisticsCommandHandler::class)->execute($command);
    }

    public function shippingCardKey(OrderShippingCardKeyCommand $command)
    {
        return app(OrderShippingCardKeyCommandHandler::class)->execute($command);
    }

    public function shippingVirtual(OrderShippingVirtualCommand $command)
    {
        return app(OrderShippingVirtualCommandHandler::class)->execute($command);
    }

    public function confirm(OrderConfirmCommand $command)
    {
        return app(OrderConfirmCommandHandler::class)->execute($command);
    }

    public function progress(OrderProgressCommand $command)
    {
        return app(OrderProgressCommandHandler::class)->execute($command);
    }


    public function sellerRemarks(OrderRemarksCommand $command)
    {
        return app(OrderRemarksCommandHandler::class)->setTradeParty(TradePartyEnums::SELLER)->execute($command);
    }

    public function buyerRemarks(OrderRemarksCommand $command)
    {
        return app(OrderRemarksCommandHandler::class)->setTradeParty(TradePartyEnums::BUYER)->execute($command);
    }


    public function sellerHidden(OrderHiddenCommand $command)
    {
        return app(OrderHiddenCommandHandler::class)->setTradeParty(TradePartyEnums::SELLER)->execute($command);
    }

    public function buyerHidden(OrderHiddenCommand $command)
    {
        return app(OrderHiddenCommandHandler::class)->setTradeParty(TradePartyEnums::BUYER)->execute($command);
    }

    public function sellerCustomStatus(OrderSellerCustomStatusCommand $command)
    {
        return app(OrderSellerCustomStatusCommandHandler::class)->execute($command);
    }


}
