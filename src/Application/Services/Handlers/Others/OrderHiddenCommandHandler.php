<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderRemarksCommand;
use RedJasmine\Order\Domain\Enums\TradePartyEnums;

class OrderHiddenCommandHandler extends AbstractOrderCommandHandler
{


    protected TradePartyEnums $tradeParty;

    public function getTradeParty() : TradePartyEnums
    {
        return $this->tradeParty;
    }

    public function setTradeParty(TradePartyEnums $tradeParty) : OrderHiddenCommandHandler
    {
        $this->tradeParty = $tradeParty;
        return $this;
    }


    public function execute(OrderRemarksCommand $command) : void
    {
        $order = $this->orderRepository->find($command->id);

        $order->hiddenOrder($this->tradeParty);

        $this->orderRepository->update($order);

    }

}
