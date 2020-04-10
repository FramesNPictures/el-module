<?php

namespace Fnp\ElModule\Features;

use Illuminate\Foundation\Application;

trait ModuleSetupWebApplication
{
    abstract public function setupWebApplication(Application $application);

    public function bootModuleSetupWebApplicationFeature(Application $application)
    {
        $this->setupWebApplication($application);
    }
}