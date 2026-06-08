<?php

namespace Fnp\ElModule\Features;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Events\Dispatcher;

trait ModuleCacheEvents
{
    /**
     * Runs after `artisan cache:clear` finishes — re-warm or rebuild whatever
     * the module keeps in the cache.
     */
    abstract public function onCacheCleared(): void;

    public function bootModuleCacheEventsFeature(Dispatcher $events): void
    {
        $events->listen(CommandFinished::class, function (CommandFinished $event): void {
            if ($event->command === 'cache:clear') {
                $this->onCacheCleared();
            }
        });
    }
}
