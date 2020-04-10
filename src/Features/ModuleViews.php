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
        $this->defineViewFolders($v = new ModuleViewsModel());

        foreach ($v->getNamespacedViews() as $namespace => $path)
            $this->loadViewsFrom($path, $namespace);

        if (count($v->getViewFolders()))
            $config->set(
                'view.paths',
                array_merge(
                    $config->get('view.path'),
                    $v->getViewFolders()
                )
            );

    }
}