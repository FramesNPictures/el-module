<?php

namespace Fnp\ElModule\Features;

use Illuminate\Database\Eloquent\Relations\Relation;

trait ModuleEnforceMorphMap
{
    abstract public function defineEnforceMorphMap(): array;

    public function bootModuleEnforceMorphMapFeature()
    {
        Relation::enforceMorphMap($this->defineEnforceMorphMap());
    }
}
