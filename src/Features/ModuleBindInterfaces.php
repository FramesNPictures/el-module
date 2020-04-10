<?php


namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Foundation\Application;

trait ModuleBindInterfaces
{
    /**
     * Returns a list of binds in a form of array (interface => concrete)
     *
     * @return array
     */
    abstract public function defineInterfaceBinds(): array;

    public function registerModuleBindInterfacesFeature(Application $application)
    {
        foreach($this->defineInterfaceBinds() as $interface=> $concrete) {
            $application->bind($interface, $concrete);
        }
    }

}