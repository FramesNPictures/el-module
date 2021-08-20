<?php

namespace Fnp\ElModule\Features;

use Fnp\ElModule\Models\ModuleViewsModel;
use Illuminate\Config\Repository as ConfigRepository;

trait ModuleViews
{
    /**
     * Return list of folders where views are stored. Those will be added
     * to the default view folder list
     *
     * @return array|string[]
     */
    abstract public function defineViewFolders(): array;

    public function bootModuleViewsFeature(ConfigRepository $config)
    {
        $folders = $this->defineViewFolders();

        if (count($folders)) {
            $config->set(
                'view.paths',
                array_merge(
                    (array) $config->get('view.paths', []),
                    $folders
                )
            );
        }
    }

    abstract protected function loadViewsFrom($path, $namespace);
}