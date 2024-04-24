<?php

namespace RedJasmine\Order\Application\Services;

use Closure;

class CommandHandlerPipeline
{
    public function executing(CommandHandler $handler, Closure $next)
    {
        return $next($handler);
    }

    public function execute(CommandHandler $handler, Closure $next)
    {
        return $next($handler);
    }

    public function executed(CommandHandler $handler, Closure $next)
    {
        return $next($handler);
    }


}
