<?php

namespace Fnp\Module\Features;

use Illuminate\Routing\Router;

trait ModuleApiRoutes
{
    /**
     * Use $router variable to set up the routes (the usual way).
     *
     * @param Router $router
     */
    abstract public function apiRoutes(Router $router);

    public function bootModuleApiRoutesFeature(Router $router)
    {
        $router->middleware(['api'])
               ->prefix('/api')
               ->group(function () use ($router) {
                   $this->apiRoutes($router);
               });
    }
}