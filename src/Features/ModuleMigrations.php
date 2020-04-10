<?php

namespace Fnp\ElModule\Features;

trait ModuleMigrations
{
    abstract protected function loadMigrationsFrom($paths);

    /**
     * Return the location of migrations (folder)
     * @return string
     */
    abstract public function defineMigrationFolders(): array;

    public function bootModuleMigrationsFeature()
    {
        $this->loadMigrationsFrom($this->defineMigrationFolders());
    }
}