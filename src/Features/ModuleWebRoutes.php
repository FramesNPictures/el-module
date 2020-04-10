<?php

namespace Fnp\Module\Features;

use Illuminate\Routing\Router;

trait ModuleWebRoutes
{
    /**
     * Use $router variable to set up the routes (the usual way).
     *
     * @param Router $router
     */
    abstract public function webRoutes(Router $router);

    public function bootModuleWebRoutesFeature(Router $router)
    {
        $router->middleware(['web'])->group(function () use ($router) {
            $this->webRoutes($router);
        });
    }
}