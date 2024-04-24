<?php

namespace RedJasmine\Order\Application\Services\Handlers\Others;

use RedJasmine\Order\Application\Services\Handlers\AbstractOrderCommandHandler;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderRemarksCommand;
use RedJasmine\Order\Domain\Enums\TradePartyEnums;

class OrderSellerRemarksCommandHandler extends OrderRemarksCommandHandler
{
    protected TradePartyEnums $tradeParty = TradePartyEnums::SELLER;
}
