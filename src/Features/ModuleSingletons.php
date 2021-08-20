<?php

namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Foundation\Application;

trait ModuleSingletons
{
    /**
     * Define singletons. Return array of classes
     * that should be treated as singleton.
     *
     * @return array|string[]
     */
    abstract public function defineSingletons(): array;

    public function registerModuleSingletonsFeature(Application $application)
    {
        foreach ($this->defineSingletons() as $abstract => $concrete) {
            $application->singleton($abstract, $concrete);
        }
    }
}