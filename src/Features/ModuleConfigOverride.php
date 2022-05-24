<?php

namespace Fnp\ElModule\Features;

use Illuminate\Support\Facades\Config;

trait ModuleConfigOverride
{
    /**
     * Return array of keys and values to be overrided.
     * Namespace as key and config file path as value.
     *
     * @return array|string[]
     */
    abstract public function defineConfigOverride(): array;

    public function bootModuleConfigOverrideFeature()
    {
        if ($this->app->configurationIsCached()) {  // Do not override if cached
            return;
        }

        foreach ($this->defineConfigOverride() as $namespace => $value) {
            Config::set($namespace, $value);
        }
    }
}
