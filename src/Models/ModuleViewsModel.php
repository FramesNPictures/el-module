<?php

namespace Fnp\ElModule\Models;

class ModuleViewsModel
{
    private $viewFolders     = [];
    private $namespacedViews = [];

    public function addViewFolder($folder)
    {
        $this->viewFolders[] = $folder;
    }

    public function addNamespacedViewFolder($namespace, $folder)
    {
        $this->namespacedViews[$namespace] = $folder;
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
    public function getNamespacedViews(): array
    {
        return $this->namespacedViews;
    }
}