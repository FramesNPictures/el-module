<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleRoutesApi
{
    /**
     * Use $router variable to set up the routes (the usual way).
     *
     * @param Router $router
     */
    abstract public function defineApiRoutes(Router $router): void;

    public function bootModuleRoutesApiFeature(Router $router)
    {
        $router->middleware(['api'])
               ->prefix('/api')
               ->group(function () use ($router) {
                   $this->defineApiRoutes($router);
               });
    }
}