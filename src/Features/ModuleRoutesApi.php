<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleRoutesApi
{
    /**
     * Use Router object to set up the routes
     * usual Laravel way.
     *
     * @param  Router  $router
     */
    abstract public function defineApiRoutes(Router $router): void;

    public function bootModuleRoutesApiFeature(Router $router)
    {
        if ($this->app->routesAreCached()) {
            return; // Do not process routes if cached
        }

        $router->middleware(['api'])
               ->prefix('/api')
               ->group(function () use ($router) {
                   $this->defineApiRoutes($router);
               });
    }
}
