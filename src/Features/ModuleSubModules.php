<?php

namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Foundation\Application;

trait ModuleSubModules
{
    /**
     * Returns a list of providers to be registered
     *
     * @return array|string[]
     */
    abstract public function defineSubModules(): array;

    public function registerModuleSubModulesFeature(Application $application)
    {
        foreach ($this->defineSubModules() as $provider) {
            $application->register($provider);
        }
    }
}