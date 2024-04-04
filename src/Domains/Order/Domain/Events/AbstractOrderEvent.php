<?php

namespace RedJasmine\Order\Domains\Order\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * 领域事件
 */
abstract class AbstractOrderEvent
{
    use Dispatchable;

    // ID 类型
    // 事件ID

    public function __construct(
        protected int $id,

    )
    {
    }
}
