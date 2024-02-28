<?php

namespace RedJasmine\Order\DataTransferObjects\Others;

use RedJasmine\Order\Actions\AbstractOrderAction;

class OrderSellerCustomStatusDTO extends AbstractOrderAction
{

    public ?string $sellerCustomStatus = null;
}
