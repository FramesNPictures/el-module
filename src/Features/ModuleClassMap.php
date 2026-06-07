<?php

namespace Fnp\ElModule\Features;

use Illuminate\Database\Eloquent\Relations\Relation;

trait ModuleClassMap
{
    /**
     * Returns an array mapping a string alias to a class name.
     * Alias as a key and fully qualified class name as a value.
     *
     * @return array|string[]
     */
    abstract public function defineClassMap(): array;

    public function bootModuleClassMapFeature()
    {
        Relation::morphMap($this->defineClassMap());
    }
}
