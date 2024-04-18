<?php

namespace RedJasmine\Order\Domain\Enums;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

enum TradePartyEnums: string
{

    use EnumsHelper;

    case ADMIN = 'admin';

    case SELLER = 'seller';

    case BUYER = 'buyer';

    case SUPPLIER = 'supplier';

    case STORE = 'store';

    case GUIDE = 'guide';

    case CHANNEL = 'channel';

}
