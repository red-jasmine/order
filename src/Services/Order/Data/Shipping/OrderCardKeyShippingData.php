<?php

namespace RedJasmine\Order\Services\Order\Data\Shipping;


use Illuminate\Support\Collection;

class OrderCardKeyShippingData extends OrderShippingData
{

    public bool $isSplit = true;

    /**
     * @var Collection<OrderCardKeyContentData>
     */
    public Collection $contents;

}
