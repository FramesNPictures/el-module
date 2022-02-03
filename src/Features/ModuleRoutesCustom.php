<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleRoutesCustom
{
    /**
     * Use Router object to set up the routes
     * usual Laravel way.
     *
     * @param  Router  $router
     */
    abstract public function defineCustomRoutes(Router $router): void;

    public function bootModuleRoutesCustomFeature(Router $router)
    {
        if ($this->app->routesAreCached()) {
            return; // Do not process routes if cached
        }

        $this->defineCustomRoutes($router);
    }
}
