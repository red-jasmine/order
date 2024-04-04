<?php

namespace RedJasmine\Order\Domains\Order\Domain\Models;

trait HasEvents
{

    protected array $events = [];


    protected function addEvent($event) : static
    {
        $this->events[] = $event;
        return $this;
    }

    public function dispatchEvents() : void
    {
        $events       = $this->events;
        $this->events = [];
        foreach ($events as $event) {
            event($event);
        }

    }

}
