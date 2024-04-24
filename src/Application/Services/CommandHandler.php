<?php

namespace RedJasmine\Order\Application\Services;

use RedJasmine\Support\Foundation\HasServiceContext;

abstract class CommandHandler implements CommandHandlerInterface
{
    use HasServiceContext;
}
