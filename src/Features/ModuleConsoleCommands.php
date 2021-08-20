<?php

namespace Fnp\ElModule\Features;

use Illuminate\Contracts\Foundation\Application;

trait ModuleConsoleCommands
{
    abstract public function commands($commands);

    /**
     * Return an array of console command's class names
     *
     * @return array|string[]
     */
    abstract public function defineConsoleCommands(): array;

    public function registerModuleConsoleCommandsFeature(Application $application)
    {
        if ($application->runningInConsole()) {
            $this->commands($this->defineConsoleCommands());
        }
    }
}