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
        $this->defineCustomRoutes($router);
    }
}