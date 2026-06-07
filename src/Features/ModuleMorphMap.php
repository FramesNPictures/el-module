<?php

namespace Fnp\ElModule\Features;

use Illuminate\Database\Eloquent\Relations\Relation;

trait ModuleMorphMap
{
    abstract public function defineMorphMap(): array;

    public function bootModuleMorphMapFeature()
    {
        Relation::morphMap($this->defineMorphMap());
    }
}
