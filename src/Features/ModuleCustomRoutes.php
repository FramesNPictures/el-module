<?php

namespace Fnp\Module\Features;

use Illuminate\Routing\Router;

trait ModuleCustomRoutes
{
    /**
     * Use $router variable to set up the routes (the usual way).
     *
     * @param Router $router
     */
    abstract public function customRoutes(Router $router);

    public function bootModuleCustomRoutesFeature(Router $router)
    {
        $this->customRoutes($router);
    }
}