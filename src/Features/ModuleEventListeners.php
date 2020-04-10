<?php


namespace Fnp\ElModule\Features;

use Illuminate\Events\Dispatcher;

trait ModuleEventListeners
{
    /**
     * Returns an array of events mapped to event listeners.
     * Event class as a key and listener class as a value.
     * For multiple listeners per event make value an array.
     *
     * @return array
     */
    abstract public function defineEventListeners(): array;

    public function bootModuleEventListenersFeature(Dispatcher $events)
    {
        foreach ($this->defineEventListeners() as $event => $listener) {

            if (!is_array($listener)) {
                $events->listen($event, $listener);
                continue;
            }

            foreach ($listener as $l) {
                $events->listen($event, $l);
            }
        }

    }
}