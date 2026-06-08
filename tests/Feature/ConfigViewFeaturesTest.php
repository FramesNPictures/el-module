<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigMerge;
use Fnp\ElModule\Features\ModuleConfigOverride;
use Fnp\ElModule\Features\ModuleNamespacedViews;
use Fnp\ElModule\Features\ModuleViews;
use Fnp\ElModule\Tests\TestCase;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class ConfigViewFeaturesTest extends TestCase
{
    #[Test]
    public function config_files_are_merged(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleConfigMerge;

            public function defineConfigMergeFiles(): array
            {
                return ['sample' => __DIR__ . '/../Fixtures/config/sample.php'];
            }
        };

        $this->app->register($module);

        $this->assertSame('bar', config('sample.foo'));
        $this->assertSame(1, config('sample.nested.a'));
    }

    #[Test]
    public function config_merge_does_not_clobber_existing_values(): void
    {
        config()->set('sample', ['foo' => 'already-set']);

        $module = new class($this->app) extends ElModule {
            use ModuleConfigMerge;

            public function defineConfigMergeFiles(): array
            {
                return ['sample' => __DIR__ . '/../Fixtures/config/sample.php'];
            }
        };

        $this->app->register($module);

        // mergeConfigFrom keeps the app's existing value and only fills gaps.
        $this->assertSame('already-set', config('sample.foo'));
        $this->assertSame(1, config('sample.nested.a'));
    }

    #[Test]
    public function config_override_replaces_values_at_runtime(): void
    {
        config()->set('services.billing.currency', 'eur');

        $module = new class($this->app) extends ElModule {
            use ModuleConfigOverride;

            public function defineConfigOverride(): array
            {
                return ['services.billing.currency' => 'usd'];
            }
        };

        $this->app->register($module);

        $this->assertSame('usd', config('services.billing.currency'));
    }

    #[Test]
    public function config_override_is_skipped_when_configuration_is_cached(): void
    {
        config()->set('services.billing.currency', 'eur');

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('configurationIsCached')->andReturn(true);

        $module = new class($app) extends ElModule {
            use ModuleConfigOverride;

            public function defineConfigOverride(): array
            {
                return ['services.billing.currency' => 'usd'];
            }
        };

        $module->bootModuleConfigOverrideFeature();

        $this->assertSame('eur', config('services.billing.currency'), 'Cached config must not be overridden.');
    }

    #[Test]
    public function view_folders_are_appended_to_the_global_view_paths(): void
    {
        $folder = __DIR__ . '/../Fixtures/views';

        $module = new class($this->app) extends ElModule {
            use ModuleViews;

            public string $folder;

            public function defineViewFolders(): array
            {
                return [$this->folder];
            }
        };
        $module->folder = $folder;

        $this->app->register($module);

        $this->assertContains($folder, config('view.paths'));
    }

    #[Test]
    public function namespaced_views_are_resolvable(): void
    {
        $folder = __DIR__ . '/../Fixtures/views';

        $module = new class($this->app) extends ElModule {
            use ModuleNamespacedViews;

            public string $folder;

            public function defineNamespacedViewFolders(): array
            {
                return ['elmodule' => $this->folder];
            }
        };
        $module->folder = $folder;

        $this->app->register($module);

        $this->assertTrue($this->app['view']->exists('elmodule::widget'));
        $this->assertArrayHasKey('elmodule', $this->app['view']->getFinder()->getHints());
    }
}
