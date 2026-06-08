<?php

namespace Fnp\ElModule\Tests\Unit;

use Fnp\ElModule\Helpers\HClassMap;
use Fnp\ElModule\Tests\Fixtures\Support\Product;
use Illuminate\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HClassMapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Relation::morphMap([], false);
    }

    protected function tearDown(): void
    {
        Relation::morphMap([], false);
        parent::tearDown();
    }

    #[Test]
    public function get_class_resolves_a_registered_alias(): void
    {
        Relation::morphMap(['product' => Product::class]);

        $this->assertSame(Product::class, HClassMap::getClass('product'));
    }

    #[Test]
    public function get_class_returns_null_for_an_unknown_alias(): void
    {
        Relation::morphMap(['product' => Product::class]);

        $this->assertNull(HClassMap::getClass('does-not-exist'));
    }

    #[Test]
    public function get_alias_resolves_a_registered_class(): void
    {
        Relation::morphMap(['product' => Product::class]);

        $this->assertSame('product', HClassMap::getAlias(Product::class));
    }

    #[Test]
    public function get_alias_returns_null_for_an_unmapped_class(): void
    {
        Relation::morphMap(['product' => Product::class]);

        $this->assertNull(HClassMap::getAlias(self::class));
    }

    #[Test]
    public function get_alias_returns_the_first_strict_match(): void
    {
        // Strict comparison (array_search $strict = true) means only an exact
        // class-string match wins, even when several aliases are registered.
        Relation::morphMap([
            'first'  => self::class,
            'second' => Product::class,
        ]);

        $this->assertSame('second', HClassMap::getAlias(Product::class));
        $this->assertSame('first', HClassMap::getAlias(self::class));
    }
}
