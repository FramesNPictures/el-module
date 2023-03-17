<?php

namespace Fnp\ElModule\Features;

use Illuminate\Support\Facades\Gate;

trait ModulePolicies
{
    /**
     * Defines the model -> policy relationship
     * @return array<class-string, class-string>
     */
    abstract public function definePolicies(): array;

    public function bootModulePoliciesFeature()
    {
        foreach($this->definePolicies() as $model=>$policy) {
            Gate::policy($model, $policy);
        }
    }
}
