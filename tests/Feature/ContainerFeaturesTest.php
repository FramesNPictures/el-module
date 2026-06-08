<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleBindInterfaces;
use Fnp\ElModule\Features\ModuleSingletons;
use Fnp\ElModule\Features\ModuleSubModules;
use Fnp\ElModule\Tests\Fixtures\Support\CartService;
use Fnp\ElModule\Tests\Fixtures\Support\EnglishGreeter;
use Fnp\ElModule\Tests\Fixtures\Support\GreeterInterface;
use Fnp\ElModule\Tests\Fixtures\Support\SubModuleProvider;
use Fnp\ElModule\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ContainerFeaturesTest extends TestCase
{
    #[Test]
    public function singletons_are_registered_and_shared(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleSingletons;

            public function defineSingletons(): array
            {
                return [CartService::class => CartService::class];
            }
        };

        $this->app->register($module);

        $this->assertSame(
            $this->app->make(CartService::class),
            $this->app->make(CartService::class),
            'Each resolution of a singleton must return the same instance.'
        );
    }

    #[Test]
    public function interface_binds_resolve_to_the_concrete(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleBindInterfaces;

            public function defineInterfaceBinds(): array
            {
                return [GreeterInterface::class => EnglishGreeter::class];
            }
        };

        $this->app->register($module);

        $this->assertInstanceOf(EnglishGreeter::class, $this->app->make(GreeterInterface::class));
    }

    #[Test]
    public function interface_binds_are_not_shared(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleBindInterfaces;

            public function defineInterfaceBinds(): array
            {
                return [GreeterInterface::class => EnglishGreeter::class];
            }
        };

        $this->app->register($module);

        // bind() (as opposed to singleton()) yields a fresh instance each time.
        $this->assertNotSame(
            $this->app->make(GreeterInterface::class),
            $this->app->make(GreeterInterface::class)
        );
    }

    #[Test]
    public function sub_modules_are_registered(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleSubModules;

            public function defineSubModules(): array
            {
                return [SubModuleProvider::class];
            }
        };

        $this->app->register($module);

        $this->assertTrue($this->app->bound('submodule.registered'));
        $this->assertTrue($this->app->make('submodule.registered'));
    }
}
