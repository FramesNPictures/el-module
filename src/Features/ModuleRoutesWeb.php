<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleRoutesWeb
{
    /**
     * Use Router object to set up the routes
     * usual laravel way
     *
     * @param  Router  $router
     */
    abstract public function defineWebRoutes(Router $router): void;

    public function bootModuleRoutesWebFeature(Router $router)
    {
        if ($this->app->routesAreCached()) {
            return; // Do not process routes if cached
        }

        $router->middleware(['web'])->group(function () use ($router) {
            $this->defineWebRoutes($router);
        });
    }
}
