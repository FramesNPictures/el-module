<?php

namespace Fnp\ElModule;

use Fnp\ElModule\Console\BuildModuleFrontendCommand;
use Fnp\ElModule\Console\BuildModulePersistenceCommand;
use Fnp\ElModule\Console\BuildModulePhpUnitCommand;
use Fnp\ElModule\Features\ModuleConfig;
use Fnp\ElModule\Features\ModuleConsole;
use Fnp\ElModule\Features\ModuleViews;

class ModuleInit extends ModuleProvider
{
    use ModuleConsole;
    use ModuleViews;
    use ModuleConfig;

    /**
     * Return an array of console command's class names
     *
     * @return array
     */
    public function consoleCommands(): array
    {
        return [
            BuildModuleFrontendCommand::class,
            BuildModulePersistenceCommand::class,
            BuildModulePhpUnitCommand::class,
        ];
    }

    /**
     * Return list of folders where views are stored:
     * Put view namespace as key and full path as value.
     *
     * @return array
     */
    public function views(): array
    {
        return [
            'fnp-module' => __DIR__ . '/Views',
        ];
    }

    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     *
     * @return array
     */
    function configFiles(): array
    {
        return [
            'module' => __DIR__.'/Config/module.php',
        ];
    }
}