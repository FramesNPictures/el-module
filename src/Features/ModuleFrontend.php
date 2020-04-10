<?php

namespace Fnp\Module\Features;

use Fnp\Module\Definitions\FrontendModuleDefinition;

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