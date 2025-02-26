<?php

namespace Fnp\ElModule\Features;

use Illuminate\Routing\Router;

trait ModuleOptimization
{
    abstract public function onOptimization(): void;

    abstract public function onOptimizationClear(): void;

    public function bootModuleOptimizationFeature(Router $router)
    {
        $this->optimizes(
            optimize: function () {
                $this->onOptimization();
            },
            clear: function () {
                $this->onOptimizationClear();
            }
        );
    }
}
