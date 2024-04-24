<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Domain\Enums\TradePartyEnums;

class OrderBuyerRemarksCommandHandler extends OrderRemarksCommandHandler
{
    protected TradePartyEnums $tradeParty = TradePartyEnums::BUYER;
}
