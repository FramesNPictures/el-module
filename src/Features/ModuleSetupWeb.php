<?php

namespace Fnp\ElModule\Features;

use Illuminate\Foundation\Application;

trait ModuleSetupWeb
{
    abstract public function setupWeb(Application $application);

    public function bootModuleSetupWebFeature(Application $application)
    {
        $this->setupWeb($application);
    }
}