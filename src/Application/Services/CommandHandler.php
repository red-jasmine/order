<?php

namespace RedJasmine\Order\Application\Services;

use Closure;
use Illuminate\Database\Eloquent\Model;
use RedJasmine\Support\Foundation\HasServiceContext;

/**
 * @property $model
 */
abstract class CommandHandler implements CommandHandlerInterface
{

    /**
     * 如何进行可配置化
     */
    use HasPipelines;

    use HasServiceContext;


    public function getModel() : Model
    {
        return $this->model;
    }

    protected function setModel(Model $model) : static
    {
        $this->model = $model;
        return $this;
    }


    protected function handle(Closure $execute, ?Closure $persistence = null) : void
    {
        $this->pipelineManager()->call('executing');
        $this->pipelineManager()->call('execute', $execute);
        // 持久化
        if ($persistence) {
            $persistence();
        }
        $this->pipelineManager()->call('executed');
    }


    protected array $executeArgs = [];


    public function getExecuteArgs() : array
    {
        return $this->executeArgs;
    }

    public function setExecuteArgs(array $executeArgs) : static
    {
        $this->executeArgs = $executeArgs;
        return $this;
    }


}
