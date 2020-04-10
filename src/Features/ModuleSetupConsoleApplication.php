<?php

namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Console\Application;

trait ModuleSetupConsoleApplication
{
    abstract public function setupConsoleApplication(Application $application);

    public function bootModuleSetupConsoleApplicationFeature(Application $application)
    {
        $this->setupConsoleApplication($application);
    }
}