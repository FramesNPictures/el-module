<?php

namespace Fnp\ElModule\Features;

use Illuminate\Foundation\Application;

trait ModuleSetupConsoleApplication
{
    abstract public function setupConsoleApplication(Application $application);

    public function bootModuleSetupConsoleApplicationFeature(Application $application)
    {
        if ($application->runningInConsole()) {
            $this->setupConsoleApplication($application);
        }
    }
}
