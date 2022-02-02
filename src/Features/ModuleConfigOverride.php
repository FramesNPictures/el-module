<?php

namespace Fnp\ElModule\Features;

use Illuminate\Support\Facades\Config;

trait ModuleConfigOverride
{
    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     *
     * @return array|string[]
     */
    abstract public function defineConfigOverride(): array;

    public function registerModuleConfigOverrideFeature()
    {
        foreach ($this->defineConfigOverride() as $namespace => $value) {
            Config::set($namespace, $value);
        }
    }
}
