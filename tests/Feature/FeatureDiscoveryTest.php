<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Services\ElModuleService;
use Fnp\ElModule\Tests\Fixtures\SpyModule;
use Fnp\ElModule\Tests\Fixtures\Support\SubModuleProvider;
use Fnp\ElModule\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FeatureDiscoveryTest extends TestCase
{
    #[Test]
    public function init_hooks_run_during_construction(): void
    {
        $module = new SpyModule($this->app);

        $this->assertSame(['init'], $module->calls);
    }

    #[Test]
    public function the_container_injects_dependencies_into_hooks(): void
    {
        $module = new SpyModule($this->app);

        $this->assertSame($this->app, $module->injectedApp);
    }

    #[Test]
    public function register_and_boot_hooks_run_in_lifecycle_order(): void
    {
        $module = new SpyModule($this->app);

        // The app is already booted in the test harness, so registering the
        // provider runs register() and then boot() back to back.
        $this->app->register($module);

        $this->assertSame(['init', 'register', 'boot'], $module->calls);
    }

    #[Test]
    public function each_phase_runs_exactly_once(): void
    {
        $module = new SpyModule($this->app);
        $this->app->register($module);

        $this->assertSame(['init', 'register', 'boot'], $module->calls);
        $this->assertCount(1, array_keys($module->calls, 'init', true));
        $this->assertCount(1, array_keys($module->calls, 'register', true));
        $this->assertCount(1, array_keys($module->calls, 'boot', true));
    }

    #[Test]
    public function a_module_with_no_feature_traits_boots_without_error(): void
    {
        $module = new class($this->app) extends ElModule {
        };

        $this->app->register($module);

        // No features means no hooks queued; the provider is inert but valid.
        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function get_module_providers_returns_only_el_modules(): void
    {
        $module = new SpyModule($this->app);
        $this->app->register($module);
        $this->app->register(new SubModuleProvider($this->app));

        $service = new ElModuleService($this->app);
        $providers = $service->getModuleProviders();

        $this->assertTrue($providers->contains($module));
        $this->assertTrue($providers->every(fn ($p) => $p instanceof ElModule));
        $this->assertFalse($providers->contains(fn ($p) => $p instanceof SubModuleProvider));
    }

    #[Test]
    public function get_service_providers_includes_plain_providers(): void
    {
        $plain = new SubModuleProvider($this->app);
        $this->app->register($plain);

        $service = new ElModuleService($this->app);

        $this->assertTrue($service->getServiceProviders()->contains($plain));
    }

    #[Test]
    public function init_on_demand_invokes_matching_hooks(): void
    {
        $module = new SpyModule($this->app);
        $this->app->register($module);
        $module->calls = []; // reset to isolate the on-demand call

        $service = new ElModuleService($this->app);
        $service->initOnDemand('Reports');

        $this->assertSame(['ondemand:reports'], $module->calls);
    }

    #[Test]
    public function init_on_demand_accepts_an_array_of_groups(): void
    {
        $module = new SpyModule($this->app);
        $this->app->register($module);
        $module->calls = [];

        $service = new ElModuleService($this->app);
        $service->initOnDemand(['Reports', 'Unknown']);

        // Only the matching group fires; unknown groups are silently skipped.
        $this->assertSame(['ondemand:reports'], $module->calls);
    }

    #[Test]
    public function init_on_demand_ignores_unknown_groups(): void
    {
        $module = new SpyModule($this->app);
        $this->app->register($module);
        $module->calls = [];

        $service = new ElModuleService($this->app);
        $service->initOnDemand('Nonexistent');

        $this->assertSame([], $module->calls);
    }
}
