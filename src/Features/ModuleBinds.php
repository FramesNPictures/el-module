<?php


namespace Fnp\Module\Features;

use Illuminate\Support\Facades\App;

trait ModuleBinds
{
    /**
     * Returns a list of binds in a form of array (interface => concrete)
     *
     * @return array
     */
    abstract public function binds(): array;

    public function registerModuleBindsFeature()
    {
        foreach($this->binds() as $interface=>$concrete) {
            App::bind($interface, $concrete);
        }
    }

}