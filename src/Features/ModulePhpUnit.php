<?php

namespace Fnp\ElModule\Features;

use Fnp\ElModule\Definitions\PhpUnitDefinition;

trait ModulePhpUnit
{
    /**
     * Return list of folders for
     *
     * @param PhpUnitDefinition $test
     */
    abstract public function phpUnit(PhpUnitDefinition $test);
}