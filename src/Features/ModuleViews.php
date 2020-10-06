<?php

namespace Fnp\ElModule\Features;

use Fnp\ElModule\Models\ModuleViewsModel;
use Illuminate\Config\Repository as ConfigRepository;

trait ModuleViews
{
    abstract protected function loadViewsFrom($path, $namespace);

    /**
     * Return list of folders where views are stored:
     * Put view namespace as key and full path as value.
     *
     * @param ModuleViewsModel $views
     */
    abstract public function defineViewFolders(ModuleViewsModel $views): void;

    public function bootModuleViewsFeature(ConfigRepository $config)
    {
        $v = new ModuleViewsModel();

        $this->defineViewFolders($v);

        foreach ($v->getNamespacedViewFolders() as $namespace => $path)
            $this->loadViewsFrom($path, $namespace);

        if (count($v->getViewFolders()))
            $config->set(
                'view.paths',
                array_merge(
                    (array)$config->get('view.path', []),
                    $v->getViewFolders()
                )
            );

    }
}