<?php

namespace Fnp\Module\Features;

use Illuminate\Support\Facades\App;

trait ModuleConsole
{
    /**
     * Return an array of console command's class names
     *
     * @return array
     */
    abstract public function consoleCommands(): array;

    public function registerModuleConsoleFeature()
    {
        if (App::runningInConsole())
            $this->commands($this->consoleCommands());
    }
}