<?php

namespace Fnp\Module\Features;

use Illuminate\Support\Facades\App;

trait ModuleProviders
{
    /**
     * Returns a list of providers to be registered
     *
     * @return array
     */
    abstract public function providers(): array;

    public function registerModuleProvidersFeature()
    {
        foreach($this->providers() as $provider) {
            App::register($provider);
        }
    }
}