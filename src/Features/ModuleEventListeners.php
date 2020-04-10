<?php


namespace Fnp\Module\Features;

use Illuminate\Support\Facades\Event;

trait ModuleEventListeners
{
    /**
     * Returns an array of events mapped to event listeners.
     * Event class as a key and listener class as a value.
     * For multiple listeners per event make value an array.
     *
     * @return array
     */
    abstract public function eventListeners(): array;

    public function bootModuleEventListenersFeature()
    {
        foreach ($this->eventListeners() as $event => $listener) {

            if (!is_array($listener)) {
                Event::listen($event, $listener);
                continue;
            }

            foreach ($listener as $l) {
                Event::listen($event, $l);
            }
        }

    }
}