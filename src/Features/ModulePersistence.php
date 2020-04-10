<?php

namespace Fnp\ElModule\Features;

trait ModulePersistence
{
    /**
     * Return the array of paths where Laraval Eloquent
     * Models as stored.
     *
     * @return array
     */
    abstract public function persistenceFolders(): array;
}