<?php

namespace RedJasmine\Order\Application\Services\Pipelines;

use Closure;
use RedJasmine\Support\Application\CommandHandler;
use RedJasmine\Support\Application\CommandHandlerPipeline;

class OrderMarkPipeline extends CommandHandlerPipeline
{
    public function executing(CommandHandler $handler, Closure $next)
    {
        $aggregate = $handler->getAggregate();

        $aggregate->title .= '-'.$handler->getPipelinesConfigKey();
        return parent::executing($handler, $next); // TODO: Change the autogenerated stub
    }


}