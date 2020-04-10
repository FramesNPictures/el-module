<?php

namespace Fnp\Module\Features;

use Fnp\Module\Definitions\PhpUnitDefinition;

trait ModulePhpUnit
{
    /**
     * Return list of folders for
     *
     * @param PhpUnitDefinition $test
     */
    abstract public function phpUnit(PhpUnitDefinition $test);
}