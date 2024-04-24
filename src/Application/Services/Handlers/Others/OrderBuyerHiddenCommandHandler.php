<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Domain\Enums\TradePartyEnums;

class OrderBuyerHiddenCommandHandler extends OrderHiddenCommandHandler
{
    protected TradePartyEnums $tradeParty = TradePartyEnums::BUYER;
}
