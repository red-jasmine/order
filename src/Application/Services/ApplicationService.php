<?php

namespace RedJasmine\Order\Application\Services;


use RedJasmine\Support\Foundation\HasServiceContext;

abstract class ApplicationService
{

    use ApplicationServiceMacroable;

    // 有服务上下文的
    use HasServiceContext;


    /**
     * @template T
     * @param T $className
     *
     * @return T
     */
    public function makeMacro(mixed $className)
    {
        if (is_string($className)) {
            $macro = app($className);
            if (method_exists($macro, 'setOperator')) {
                $macro->setOperator($this->getOperator());
            }
            return $macro;
        }
        return $className;

    }


}
