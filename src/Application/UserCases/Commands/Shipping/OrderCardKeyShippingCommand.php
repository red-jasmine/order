<?php

namespace RedJasmine\Order\Application\UserCases\Commands\Shipping;

use RedJasmine\Order\Domain\Models\Enums\CardKeys\OrderCardKeyContentTypeEnum;
use RedJasmine\Order\Domain\Models\Enums\CardKeys\OrderCardKeyStatusEnum;
use RedJasmine\Support\Data\Data;

class OrderCardKeyShippingCommand extends Data
{
    public int $id;

    public int $orderProductId;


    public OrderCardKeyContentTypeEnum $contentType = OrderCardKeyContentTypeEnum::TEXT;

    /**
     * 内容
     * @var string
     */
    public string $content;


    /**
     * 数量
     * @var int
     */
    public int $num = 1;


    /**
     * @var string|null
     */
    public ?string $sourceType = null;

    /**
     * @var string|null
     */
    public ?string $sourceId = null;


    public OrderCardKeyStatusEnum $status = OrderCardKeyStatusEnum::SHIPPED;


}
