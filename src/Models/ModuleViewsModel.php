<?php

namespace Fnp\ElModule\Models;

class ModuleViewsModel
{
    private $viewFolders           = [];
    private $namespacedViewFolders = [];

    public function addViewFolder($folder): void
    {
        $this->viewFolders[] = $folder;
    }

    public function addNamespacedViewFolder($namespace, $folder): void
    {
        $this->namespacedViewFolders[$namespace] = $folder;
    }

    /**
     * @return array
     */
    public function getViewFolders(): array
    {
        return $this->viewFolders;
    }

    /**
     * @return array
     */
    public function getNamespacedViewFolders(): array
    {
        return $this->namespacedViewFolders;
    }
}