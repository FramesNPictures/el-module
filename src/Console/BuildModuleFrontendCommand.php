<?php

namespace Fnp\Module\Console;

use Fnp\Module\Definitions\FrontendModuleDefinition;
use Fnp\Module\ModuleProvider;
use Fnp\Module\Services\ModuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class BuildModuleFrontendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Frontend Module Files';

    /**
     * @var Collection
     */
    protected $frontendModules;

    /**
     * @var Collection
     */
    protected $frontendFiles;

    /**
     * @var string
     */
    protected $modulePath;

    public function __construct()
    {
        parent::__construct();
        $this->frontendModules = new Collection();
        $this->frontendFiles   = new Collection();
        $this->modulePath      = config('module.path', resource_path('module'));
    }

    public function handle(ModuleService $repository)
    {
        $this->buildFolder();

        $providers = $repository->getModuleProviders();

        foreach ($providers as $provider)
            $this->processFrontend($provider);


        foreach ($this->frontendModules as $definition)
            $this->buildFrontend($definition);

        $this->dumpFiles();
    }

    protected function buildFolder()
    {
        if (!file_exists($this->modulePath)) {
            $this->info('Creating module resource folder...');
            mkdir($this->modulePath);
        }
    }

    protected function processFrontend(ModuleProvider $provider)
    {
        if (!method_exists($provider, 'frontend'))
            return;

        $definition = new FrontendModuleDefinition();
        $provider->frontend($definition);

        if (!$this->frontendModules->has($definition->getHandle())) {
            $this->frontendModules->put($definition->getHandle(), $definition);
        } else {
            /** @var FrontendModuleDefinition $existing */
            $existing = $this->frontendModules->get($definition->getHandle());
            $existing->merge($definition);
        }
    }

    protected function buildFrontend(FrontendModuleDefinition $definition)
    {
        $this->build('javascript', 'js', $definition);
        $this->build('less', 'less', $definition);
        $this->build('sass', 'scss', $definition);
    }

    protected function build($name, $extension, FrontendModuleDefinition $definition)
    {
        $content = View::make('fnp-module::frontend.' . $name, compact('definition'));

        $this->frontendFiles->put(
            $definition->getTargetModuleFilePath($extension),
            $content->render()
        );
    }

    protected function dumpFiles()
    {
        foreach ($this->frontendFiles as $file => $content) {
            file_put_contents($file, $content);
        }
    }
}
