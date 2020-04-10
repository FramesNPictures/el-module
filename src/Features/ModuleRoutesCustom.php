<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleRoutesCustom
{
    /**
     * Use $router variable to set up the routes (the usual way).
     *
     * @param Router $router
     */
    abstract public function defineCustomRoutes(Router $router): void;

    public function bootModuleRoutesCustomFeature(Router $router)
    {
        $this->defineCustomRoutes($router);
    }
}