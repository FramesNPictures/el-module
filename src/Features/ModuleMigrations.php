<?php

namespace Fnp\Module\Features;

trait ModuleMigrations
{
    abstract protected function loadMigrationsFrom($paths);

    /**
     * Return the location of migrations (folder)
     * @return string
     */
    abstract public function migrationsFolder(): string;

    public function bootModuleMigrationsFeature()
    {
        $this->loadMigrationsFrom($this->migrationsFolder());
    }
}