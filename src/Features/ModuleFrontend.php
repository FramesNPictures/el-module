<?php

namespace Fnp\ElModule\Features;

use Fnp\ElModule\Definitions\FrontendModuleDefinition;

trait ModuleFrontend
{
    /**
     * Define frontend Vue Module by setting the FrontendVue
     * Object properties.
     *
     * @param FrontendModuleDefinition $fe
     *
     * @return void
     */
    abstract public function frontend(FrontendModuleDefinition $fe);
}