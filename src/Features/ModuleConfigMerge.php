<?php

namespace Fnp\ElModule\Features;

trait ModuleConfigMerge
{
    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     *
     * @return array|string[]
     */
    abstract public function defineConfigMergeFiles(): array;

    public function registerModuleConfigMergeFeature()
    {
        foreach ($this->defineConfigMergeFiles() as $namespace => $file) {
            $this->mergeConfigFrom($file, $namespace);
        }
    }

    abstract protected function mergeConfigFrom($path, $key);
}
