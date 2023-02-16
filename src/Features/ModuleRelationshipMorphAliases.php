<?php

namespace Fnp\ElModule\Features;

use Illuminate\Database\Eloquent\Relations\Relation;

trait ModuleRelationshipMorphAliases
{
    abstract public function defineRelationshipMorphAliases(): array;

    public function bootModuleRelationshipMorphAliasesFeature()
    {
        Relation::enforceMorphMap($this->defineRelationshipMorphAliases());
    }
}
