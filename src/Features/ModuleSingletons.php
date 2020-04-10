<?php

namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Foundation\Application;

trait ModuleSingletons
{
    abstract public function defineSingletons(): array;

    public function registerModuleSingletonsFeature(Application $application)
    {
        foreach ($this->defineSingletons() as $abstract => $concrete) {
            $application->singleton($abstract, $concrete);
        }
    }
}