<?php

namespace RedJasmine\Order\Domains\Order\Application\UserCases\Commands\Shipping;

use RedJasmine\Support\Data\Data;

class OrderShippingCardKeyCommand extends Data
{
    public int $id;

    public int $orderProductId;

    /**
     * 内容
     * @var string
     */
    public string $content;


    /**
     * 扩展信息
     * @var array
     */
    public array $extends = [];


    // TODO 状态
    public string $status = 'init';

}
