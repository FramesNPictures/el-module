<?php

namespace Fnp\ElModule\Features;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Events\Dispatcher;

trait ModuleOptimizationEvents
{
    /**
     * Runs after `artisan optimize` finishes — cache/warm whatever the module
     * needs.
     */
    abstract public function onOptimization(): void;

    /**
     * Runs after `artisan optimize:clear` finishes — discard whatever
     * onOptimization() built.
     */
    abstract public function onOptimizationClear(): void;

    public function bootModuleOptimizationEventsFeature(Dispatcher $events): void
    {
        $events->listen(CommandFinished::class, function (CommandFinished $event): void {
            if ($event->command === 'optimize') {
                $this->onOptimization();
            }

            if ($event->command === 'optimize:clear') {
                $this->onOptimizationClear();
            }
        });
    }
}
