<?php

namespace Fnp\ElModule\Tests\Feature;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleClassMap;
use Fnp\ElModule\Features\ModuleEnforceMorphMap;
use Fnp\ElModule\Features\ModuleMorphMap;
use Fnp\ElModule\Features\ModulePolicies;
use Fnp\ElModule\Tests\Fixtures\Support\Product;
use Fnp\ElModule\Tests\Fixtures\Support\ProductPolicy;
use Fnp\ElModule\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\Test;

class EloquentFeaturesTest extends TestCase
{
    #[Test]
    public function morph_map_entries_are_registered(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleMorphMap;

            public function defineMorphMap(): array
            {
                return ['product' => Product::class];
            }
        };

        $this->app->register($module);

        $this->assertSame(Product::class, Relation::getMorphedModel('product'));
    }

    #[Test]
    public function class_map_entries_are_registered_in_the_morph_map(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModuleClassMap;

            public function defineClassMap(): array
            {
                return ['product' => Product::class];
            }
        };

        $this->app->register($module);

        $this->assertArrayHasKey('product', Relation::morphMap());
        $this->assertSame(Product::class, Relation::morphMap()['product']);
    }

    #[Test]
    public function enforce_morph_map_requires_a_map(): void
    {
        $this->assertFalse(Relation::requiresMorphMap());

        $module = new class($this->app) extends ElModule {
            use ModuleEnforceMorphMap;

            public function defineEnforceMorphMap(): array
            {
                return ['product' => Product::class];
            }
        };

        $this->app->register($module);

        $this->assertTrue(Relation::requiresMorphMap());
        $this->assertSame(Product::class, Relation::getMorphedModel('product'));
    }

    #[Test]
    public function policies_are_registered_with_the_gate(): void
    {
        $module = new class($this->app) extends ElModule {
            use ModulePolicies;

            public function definePolicies(): array
            {
                return [Product::class => ProductPolicy::class];
            }
        };

        $this->app->register($module);

        $this->assertInstanceOf(ProductPolicy::class, Gate::getPolicyFor(Product::class));
    }
}
