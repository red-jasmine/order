<?php

namespace RedJasmine\Order\Application\Services;

use RedJasmine\Support\Foundation\Service\Pipeline;

trait HasPipelines
{
    protected array $pipelines = [];

    public function addPipeline($pipeline) : static
    {
        $this->pipelines[] = $pipeline;
        return $this;
    }


    /**
     * 管道组合
     * @var Pipeline
     */
    protected Pipeline $pipelineManager;

    protected function pipelineManager() : Pipeline
    {
        return $this->pipelineManager = $this->pipelineManager ?? $this->newPipelineManager($this);
    }

    protected function newPipelineManager($passable) : Pipeline
    {
        return app(Pipeline::class)->send($passable)->pipe($this->pipelines);
    }


}
