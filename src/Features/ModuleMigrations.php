<?php

namespace Fnp\ElModule\Features;

trait ModuleMigrations
{
    /**
     * Return the location of migrations (folder)
     *
     * @return array|string[]
     */
    abstract public function defineMigrationFolders(): array;

    public function bootModuleMigrationsFeature()
    {
        $this->loadMigrationsFrom($this->defineMigrationFolders());
    }

    abstract protected function loadMigrationsFrom($paths);
}