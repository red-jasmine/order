<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Domain\Enums\TradePartyEnums;

class OrderSellerHiddenCommandHandler extends OrderHiddenCommandHandler
{
    protected TradePartyEnums $tradeParty = TradePartyEnums::SELLER;
}
