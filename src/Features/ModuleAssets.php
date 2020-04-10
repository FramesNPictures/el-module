<?php


namespace Fnp\Module\Features;


trait ModuleAssets
{
    abstract protected function publishes(array $paths, $groups = NULL);

    /**
     * Return Asset's source and target folder array.
     * Source as a key and target as an value.
     * @return array
     */
    abstract public function assetsFolders(): array;

    /**
     * Return Assset's publish tag
     * @return string
     */
    abstract public function assetsTag(): string;

    public function bootModuleAssetsFeature()
    {
        $this->publishes($this->assetsFolders(), $this->assetsTag());
    }
}