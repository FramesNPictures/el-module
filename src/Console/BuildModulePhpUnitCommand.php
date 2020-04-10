<?php


namespace Fnp\Module\Console;


use Fnp\Module\Definitions\PhpUnitDefinition;
use Fnp\Module\Features\ModulePhpUnit;
use Fnp\Module\Services\ModuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BuildModulePhpUnitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:phpunit {--S|src= : Specify source folder}';


    protected $suites = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Module Persistence IDE Helpers';

    public function handle(ModuleService $repository)
    {
        $providers = $repository->getModuleProviders();

        /** @var ModulePhpUnit $provider */
        foreach ($providers as $provider) {

            if (!method_exists($provider, 'phpUnit'))
                continue;

            $definition = new PhpUnitDefinition();

            $provider->phpUnit($definition);

            foreach ($definition->getTestSuites() as $testSuite => $paths)
                foreach ($paths as $path)
                    $this->suites[ $testSuite ][] = $path;

        }

        $this->updatePhpUnit();
    }

    public function updatePhpUnit()
    {
        $dom = new \DOMDocument();
        $dom->load(base_path('phpunit.xml'));
        $root           = $dom->documentElement;
        $testSuites     = $root->getElementsByTagName('testsuite');
        $testSuitesRoot = $root->getElementsByTagName('testsuites')->item(0);

        while ($testSuites->length) {
            $testSuitesRoot->removeChild($testSuites->item(0));
            $testSuites = $root->getElementsByTagName('testsuite');
        }

        foreach ($this->suites as $suite => $paths) {
            $s = $dom->createElement('testsuite');
            $s->setAttribute('name', $suite);

            foreach ($paths as $path) {

                $relativePath = str_replace(base_path() . '/', '', $path);

                if (Str::startsWith($relativePath, 'vendor/'))
                    continue;

                $d = $dom->createELement('directory', $relativePath);
                $d->setAttribute('suffix', 'Test.php');
                $s->appendChild($d);
            }

            $testSuitesRoot->appendChild($s);
        }

        $dom->save(base_path('phpunit.xml'));
    }
}