<?php

namespace Fnp\ElModule\Tests\Unit;

use Fnp\ElModule\Services\ElModuleService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ElModuleServiceTest extends TestCase
{
    public static function methodNameProvider(): array
    {
        return [
            // The canonical feature-hook shape used throughout the package.
            'prefix + studly trait + suffix' => ['boot', 'ModuleRoutesWeb', 'Feature', 'bootModuleRoutesWebFeature'],
            'init prefix'                     => ['init', 'ModuleViews', 'Feature', 'initModuleViewsFeature'],
            'register prefix'                 => ['register', 'ModuleSingletons', 'Feature', 'registerModuleSingletonsFeature'],

            // No prefix => the name is lower-camel-cased and no leading ucfirst.
            'no prefix, studly name'  => [null, 'ModuleRoutesWeb', null, 'moduleRoutesWeb'],
            'empty-string prefix'     => ['', 'Test', null, 'test'],

            // Suffix handling.
            'no suffix'        => ['boot', 'ModuleViews', null, 'bootModuleViews'],
            'empty suffix'     => ['boot', 'ModuleViews', '', 'bootModuleViews'],
            'lowercase suffix' => ['init', 'Thing', 'ondemand', 'initThingOndemand'],

            // Separators are normalised to word boundaries.
            'space separated' => ['init', 'some name', 'OnDemand', 'initSomeNameOnDemand'],
            'dash separated'  => ['boot', 'foo-bar', null, 'bootFooBar'],
            'dot separated'   => ['boot', 'foo.bar', null, 'bootFooBar'],

            // All-uppercase names are lower-cased before camel-casing.
            'all caps name'             => ['init', 'API', 'OnDemand', 'initApiOnDemand'],
            'all caps, no prefix'       => [null, 'API', null, 'api'],
        ];
    }

    #[Test]
    #[DataProvider('methodNameProvider')]
    public function it_builds_method_names(?string $prefix, string $name, ?string $suffix, string $expected): void
    {
        $this->assertSame($expected, ElModuleService::methodName($prefix, $name, $suffix));
    }

    #[Test]
    public function method_name_is_idempotent_for_already_studly_traits(): void
    {
        // class_basename() of a feature trait feeds methodName(); make sure a
        // StudlyCase trait name round-trips to exactly the published hook name.
        $this->assertSame(
            'bootModuleConfigOverrideFeature',
            ElModuleService::methodName('boot', 'ModuleConfigOverride', 'Feature')
        );
    }

    #[Test]
    public function method_exists_returns_resolved_name_when_present(): void
    {
        $object = new class {
            public function bootModuleThingFeature(): void
            {
            }
        };

        $this->assertSame(
            'bootModuleThingFeature',
            ElModuleService::methodExists($object, 'boot', 'ModuleThing', 'Feature')
        );
    }

    #[Test]
    public function method_exists_returns_null_when_absent(): void
    {
        $object = new class {
        };

        $this->assertNull(
            ElModuleService::methodExists($object, 'boot', 'Nonexistent', 'Feature')
        );
    }

    #[Test]
    public function method_exists_accepts_a_class_string(): void
    {
        $this->assertSame(
            'methodName',
            ElModuleService::methodExists(ElModuleService::class, null, 'methodName')
        );
    }
}
