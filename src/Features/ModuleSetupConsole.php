<?php

namespace Fnp\Module\Features;

use Illuminate\Contracts\Console\Application;

trait ModuleSetupConsole
{
    abstract public function setupConsole(Application $application);

    public function bootModuleSetupConsoleFeature(Application $application)
    {
        $this->setupConsole($application);
    }
}