<?php

namespace Fnp\Module\Features;

use Illuminate\Foundation\Application;

trait ModuleSetupWeb
{
    abstract public function setupWeb(Application $application);

    public function bootModuleSetupWebFeature(Application $application)
    {
        $this->setupWeb($application);
    }
}