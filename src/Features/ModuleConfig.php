<?php

namespace Fnp\Module\Features;

trait ModuleConfig
{
    abstract protected function mergeConfigFrom($path, $key);

    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     * @return array
     */
    abstract function configFiles(): array;

    public function registerModuleConfigFeature()
    {
        foreach($this->configFiles() as $namespace=>$file)
            $this->mergeConfigFrom($file, $namespace);
    }
}