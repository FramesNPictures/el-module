<?php

namespace Fnp\ElModule\Features;

trait ModuleConfig
{
    abstract protected function mergeConfigFrom($path, $key);

    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     *
     * @return array
     */
    abstract function defineConfigFiles(): array;

    public function registerModuleConfigFeature()
    {
        foreach ($this->defineConfigFiles() as $namespace => $file)
            $this->mergeConfigFrom($file, $namespace);
    }
}