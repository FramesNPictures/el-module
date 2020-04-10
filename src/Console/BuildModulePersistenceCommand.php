<?php

namespace Fnp\Module\Console;

use Fnp\Module\ModuleProvider;
use Fnp\Module\Services\ModuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class BuildModulePersistenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:persistence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Module Persistence IDE Helpers';

    /**
     * @var Collection
     */
    protected $persistencePaths;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->persistencePaths = new Collection();
    }

    public function handle(ModuleService $repository)
    {
        $providers          = $repository->getModuleProviders();

        foreach ($providers as $provider)
            $this->processPersistence($provider);

        $this->buildPersistence();
    }

    protected function processPersistence(ModuleProvider $provider)
    {
        if (!method_exists($provider, 'persistenceFolders'))
            return;

        if (!class_exists('Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider', TRUE))
            return;

        $folders = $provider->persistenceFolders();
        $composer = json_decode(file_get_contents(base_path('composer.json')), TRUE);
        $sources = array_values($composer['autoload']['psr-4']);

        foreach ($folders as $folder) {
            $relativeFolder = str_replace(base_path() . '/', '', $folder);

            if (Str::startsWith($relativeFolder, $sources)) {
                $cmd = 'ide-helper:models --write  --reset --dir="' . $relativeFolder . '"';
                Artisan::call($cmd);
            } else {
                $this->persistencePaths->push($relativeFolder);
            }
        }
    }

    protected function buildPersistence()
    {
        $cmd = 'ide-helper:models --nowrite';

        foreach ($this->persistencePaths as $folder) {
            $cmd .= ' --dir="' . $folder . '"';
        }

        Artisan::call($cmd);
    }
}
