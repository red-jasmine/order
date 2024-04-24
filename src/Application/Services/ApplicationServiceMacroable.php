<?php

namespace RedJasmine\Order\Application\Services;

use BadMethodCallException;
use Illuminate\Support\Traits\Macroable;

trait ApplicationServiceMacroable
{

    use Macroable {
        __call as macroCall;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (!static::hasMacro($method)) {

            throw new BadMethodCallException(sprintf(
                                                 'Method %s::%s does not exist.', static::class, $method
                                             ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        if (method_exists($this, 'makeMarco')) {
            $macro = $this->makeMarco($macro);
        }
        if (method_exists($macro, 'execute')) {
            return $macro->execute(...$parameters);
        }

        return $macro(...$parameters);
    }


}
