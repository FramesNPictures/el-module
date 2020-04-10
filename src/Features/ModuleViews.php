<?php

namespace Fnp\Module\Features;

trait ModuleViews
{
    abstract protected function loadViewsFrom($path, $namespace);


    /**
     * Return list of folders where views are stored:
     * Put view namespace as key and full path as value.
     * @return array
     */
    abstract public function views(): array;

    public function bootModuleViewsFeature()
    {
        foreach($this->views() as $namespace=> $path)
        $this->loadViewsFrom($path, $namespace);
    }
}