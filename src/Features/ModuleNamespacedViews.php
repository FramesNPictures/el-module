<?php

namespace Fnp\ElModule\Features;

trait ModuleNamespacedViews
{
    /**
     * Return list of folders where views are stored:
     * Put view namespace as key and full path as value.
     *
     * @return array
     */
    abstract public function defineNamespacedViewFolders(): array;

    public function bootModuleNamespacedViewsFeature()
    {
        foreach ($this->defineNamespacedViewFolders() as $namespace => $path) {
            $this->loadViewsFrom($path, $namespace);
        }
    }

    abstract protected function loadViewsFrom($path, $namespace);
}