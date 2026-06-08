<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleRoutesApi;
use Fnp\ElModule\Features\ModuleRoutesCustom;
use Fnp\ElModule\Features\ModuleRoutesWeb;
use Fnp\ElModule\Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class RoutingFeaturesTest extends TestCase
{
    #[Test]
    public function web_routes_are_registered_with_the_web_middleware_group(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleRoutesWeb;

            public function defineWebRoutes(Router $router): void
            {
                $router->get('/blog', fn () => 'ok')->name('blog.index');
            }
        };

        $this->app->register($module);

        $route = $this->findRouteByName('blog.index');

        $this->assertNotNull($route, 'The web route should be registered.');
        $this->assertSame('blog', $route->uri());
        $this->assertContains('web', $route->middleware());
    }

    #[Test]
    public function api_routes_are_prefixed_and_use_the_api_middleware_group(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleRoutesApi;

            public function defineApiRoutes(Router $router): void
            {
                $router->get('/things', fn () => 'ok')->name('things.index');
            }
        };

        $this->app->register($module);

        $route = $this->findRouteByName('things.index');

        $this->assertNotNull($route);
        $this->assertSame('api/things', $route->uri());
        $this->assertContains('api', $route->middleware());
    }

    #[Test]
    public function custom_routes_have_no_prefix_or_middleware_group(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleRoutesCustom;

            public function defineCustomRoutes(Router $router): void
            {
                $router->get('/raw', fn () => 'ok')->name('raw.index');
            }
        };

        $this->app->register($module);

        $route = $this->findRouteByName('raw.index');

        $this->assertNotNull($route);
        $this->assertSame('raw', $route->uri());
        $this->assertNotContains('web', $route->middleware());
        $this->assertNotContains('api', $route->middleware());
    }

    #[Test]
    public function web_routes_are_skipped_when_routes_are_cached(): void
    {
        $router = $this->app['router'];
        $before = count($router->getRoutes());

        $module = $this->moduleWithCachedRoutes();
        $module->bootModuleRoutesWebFeature($router);

        $this->assertCount($before, $router->getRoutes(), 'Cached routes must not be re-registered.');
    }

    /**
     * Find a registered route by name by iterating the collection. We avoid
     * RouteCollection::getByName() because its name lookup is built lazily and
     * is stale for routes added by a provider booted after the app.
     */
    private function findRouteByName(string $name): ?Route
    {
        foreach ($this->app['router']->getRoutes() as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }

    private function moduleWithCachedRoutes(): ElModule
    {
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('routesAreCached')->andReturn(true);

        return new class($app) extends ElModule {
            use ModuleRoutesWeb;

            public function defineWebRoutes(Router $router): void
            {
                $router->get('/should-not-exist', fn () => 'ok');
            }
        };
    }
}
