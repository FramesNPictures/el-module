# el-module

**A declarative way to build Laravel Service Providers.**

`framesnpictures/el-module` lets you compose a service provider out of small,
single-purpose **feature traits** instead of hand-writing `register()` and
`boot()` methods. You `use` a trait, implement one short `define…()` method, and
the module registers it at the correct point in the application lifecycle for
you.

## Why this exists

A vanilla Laravel `ServiceProvider` has two lifecycle methods, and you have to
remember **which concern goes where**:

- Container bindings (singletons, interface binds) belong in `register()`.
- Routes, views, events, policies, migrations and most wiring belong in
  `boot()` — and some of it only when running in the console, or only when
  routes/config aren't cached.

Get that wrong and you end up with subtle, hard-to-spot bugs (binding something
in `boot()` that another provider needs in `register()`, registering routes that
break when cached, and so on). On top of that, everything piles into the same
two fat methods.

`ElModule` removes that burden:

- **No more remembering `register()` vs `boot()`.** Each feature trait already
  encodes the correct phase. You just declare *what*, not *when*.
- **Separation of concerns.** Every concern lives in its own tiny method
  (`defineWebRoutes()`, `defineSingletons()`, `definePolicies()`, …) instead of
  one giant `boot()`.
- **Cleaner, declarative providers.** A module reads as a list of capabilities
  it opts into, plus the data each capability needs.

## Installation

```bash
composer require framesnpictures/el-module
```

Requires PHP `^8` and a Laravel application (the `Illuminate\*` components are
provided by the host app). It has no other third-party dependencies.

## Quick start

Extend `ElModule`, `use` the features you need, and implement their `define…()`
methods:

```php
<?php

namespace App\Billing;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigOverride;
use Fnp\ElModule\Features\ModuleMigrations;
use Fnp\ElModule\Features\ModuleSchedule;
use App\Billing\Jobs\ChargeDueInvoices;
use Illuminate\Console\Scheduling\Schedule;

class BillingModule extends ElModule
{
    use ModuleMigrations;
    use ModuleConfigOverride;
    use ModuleSchedule;

    public function defineMigrationFolders(): array
    {
        return [
            __DIR__ . '/../database/migrations',
        ];
    }

    public function defineConfigOverride(): array
    {
        return [
            'services.billing.currency' => 'usd',
        ];
    }

    public function defineSchedule(Schedule $scheduler): void
    {
        $scheduler->job(ChargeDueInvoices::class)
            ->dailyAt('03:00')
            ->withoutOverlapping();
    }
}
```

Register it like any other service provider (`config/app.php` or
`bootstrap/providers.php`):

```php
App\Billing\BillingModule::class,
```

That's the whole provider — no `register()`, no `boot()`, no remembering which
goes where.

## How it works

`ElModule` extends Laravel's `ServiceProvider`. When the provider is
constructed it scans every trait on the class (via `class_uses_recursive()`) and,
for each one, looks for a hook method named after the trait:

| Lifecycle phase | Hook method pattern | When it runs |
| --- | --- | --- |
| init | `init<Trait>Feature` | Immediately, in the constructor |
| register | `register<Trait>Feature` | During the provider's `register()` |
| boot | `boot<Trait>Feature` | During the provider's `boot()` |

So a trait named `ModuleRoutesWeb` is wired up through
`bootModuleRoutesWebFeature()`. Each hook is invoked through the container
(`$this->app->call()`), which means **you can type-hint dependencies** on the
hook and on most `define…()` methods (e.g. `Router`, `Schedule`, `Dispatcher`)
and they'll be injected.

You normally never write the hook methods — the traits provide them. You only
implement the abstract `define…()` (or `on…()` / `setup…()`) method each trait
declares.

### IDE support

Because every feature is an `abstract` method on the trait, your IDE does the
discovery for you. As soon as you add `use ModuleX;` to a module, the class is
flagged as having unimplemented abstract methods, and PhpStorm / VS Code (Intelephense)
will offer an **"Implement methods"** quick-fix that generates the correct
`define…()` stub — right name, parameters and return type — ready to fill in. You
don't have to remember each trait's method signature; let the IDE suggest it.

## Feature reference

### Container bindings — `register` phase

| Trait | Implement | Purpose |
| --- | --- | --- |
| `ModuleSingletons` | `defineSingletons(): array` | Register `abstract => concrete` singletons |
| `ModuleBindInterfaces` | `defineInterfaceBinds(): array` | Bind `interface => concrete` implementations |
| `ModuleSubModules` | `defineSubModules(): array` | Register additional service providers as sub-modules |

```php
use Fnp\ElModule\Features\ModuleSingletons;
use Fnp\ElModule\Features\ModuleBindInterfaces;

class ShopModule extends ElModule
{
    use ModuleSingletons;
    use ModuleBindInterfaces;

    public function defineSingletons(): array
    {
        return [
            \App\Shop\Cart::class => \App\Shop\Cart::class,
        ];
    }

    public function defineInterfaceBinds(): array
    {
        return [
            \App\Shop\Contracts\PaymentGateway::class => \App\Shop\Stripe\StripeGateway::class,
        ];
    }
}
```

### Routing — `boot` phase

| Trait | Implement | Purpose |
| --- | --- | --- |
| `ModuleRoutesWeb` | `defineWebRoutes(Router $router): void` | Routes wrapped in the `web` middleware group |
| `ModuleRoutesApi` | `defineApiRoutes(Router $router): void` | Routes wrapped in the `api` middleware group, prefixed with `/api` |
| `ModuleRoutesCustom` | `defineCustomRoutes(Router $router): void` | Routes with no automatic middleware/prefix |

All three skip registration automatically when routes are cached.

```php
use Fnp\ElModule\Features\ModuleRoutesWeb;
use Illuminate\Routing\Router;

class BlogModule extends ElModule
{
    use ModuleRoutesWeb;

    public function defineWebRoutes(Router $router): void
    {
        $router->get('/blog', [\App\Blog\BlogController::class, 'index']);
    }
}
```

### Views & configuration

| Trait | Phase | Implement | Purpose |
| --- | --- | --- | --- |
| `ModuleViews` | boot | `defineViewFolders(): array` | Add folders to the global view path list |
| `ModuleNamespacedViews` | boot | `defineNamespacedViewFolders(): array` | Register `namespace => path` view namespaces (e.g. `admin::`) |
| `ModuleConfigMerge` | register | `defineConfigMergeFiles(): array` | Merge `key => file` config files into the app config |
| `ModuleConfigOverride` | boot | `defineConfigOverride(): array` | Override `key => value` config at runtime (skipped when config is cached) |

```php
use Fnp\ElModule\Features\ModuleNamespacedViews;

public function defineNamespacedViewFolders(): array
{
    return [
        'admin' => __DIR__ . '/../resources/views',
    ];
}
// ... then reference views as view('admin::dashboard')
```

### Eloquent — morph maps & policies — `boot` phase

| Trait | Implement | Registers via |
| --- | --- | --- |
| `ModuleMorphMap` | `defineMorphMap(): array` | `Relation::morphMap()` |
| `ModuleEnforceMorphMap` | `defineEnforceMorphMap(): array` | `Relation::enforceMorphMap()` (rejects unmapped models) |
| `ModuleClassMap` | `defineClassMap(): array` | `Relation::morphMap()` — register `alias => class` for lookup with `HClassMap` |
| `ModulePolicies` | `definePolicies(): array` | `Gate::policy()` for each `model => policy` |

```php
use Fnp\ElModule\Features\ModuleClassMap;
use Fnp\ElModule\Features\ModulePolicies;

class CatalogModule extends ElModule
{
    use ModuleClassMap;
    use ModulePolicies;

    public function defineClassMap(): array
    {
        return [
            'product'  => \App\Catalog\Product::class,
            'category' => \App\Catalog\Category::class,
        ];
    }

    public function definePolicies(): array
    {
        return [
            \App\Catalog\Product::class => \App\Catalog\Policies\ProductPolicy::class,
        ];
    }
}
```

> `ModuleMorphMap` and `ModuleClassMap` both populate Laravel's morph map. Use
> `ModuleMorphMap` when you think of it as polymorphic-relation aliases, and
> `ModuleClassMap` when you primarily want `alias <-> class` lookups via the
> `HClassMap` helper. Use `ModuleEnforceMorphMap` when you also want Laravel to
> reject any model that isn't in the map.

### Console & application lifecycle

| Trait | Phase | Implement | Purpose |
| --- | --- | --- | --- |
| `ModuleConsoleCommands` | register | `defineConsoleCommands(): array` | Register Artisan commands (console only) |
| `ModuleMigrations` | boot | `defineMigrationFolders(): array` | Load migrations from folders (console only) |
| `ModuleSchedule` | boot | `defineSchedule(Schedule $scheduler): void` | Define scheduled jobs/tasks (console only) |
| `ModuleEventListeners` | boot | `defineEventListeners(): array` | Map `event => listener` (value may be an array of listeners) |
| `ModuleOptimizationEvents` | boot | `onOptimization(): void` / `onOptimizationClear(): void` | Run a hook after `artisan optimize` / `optimize:clear` (via the `CommandFinished` event) |
| `ModuleCacheEvents` | boot | `onCacheCleared(): void` | Run a hook after `artisan cache:clear` (via the `CommandFinished` event) |
| `ModuleSetupConsoleApplication` | boot | `setupConsoleApplication(Application $application)` | Arbitrary setup that runs only in the console |
| `ModuleSetupWebApplication` | boot | `setupWebApplication(Application $application)` | Arbitrary setup that runs only for web requests |

```php
use Fnp\ElModule\Features\ModuleEventListeners;

public function defineEventListeners(): array
{
    return [
        \App\Orders\Events\OrderPlaced::class => [
            \App\Orders\Listeners\SendConfirmation::class,
            \App\Orders\Listeners\NotifyWarehouse::class,
        ],
    ];
}
```

## Helpers

The `Fnp\ElModule\Helpers` namespace ships a few small utilities.

### `HClassMap`

Resolves between morph aliases and class names using the map registered by
`ModuleClassMap` / `ModuleMorphMap`:

```php
use Fnp\ElModule\Helpers\HClassMap;

HClassMap::getClass('product');                   // 'App\Catalog\Product' or null
HClassMap::getAlias(\App\Catalog\Product::class); // 'product' or null
```

- `getClass(string $alias): ?string` — the fully qualified class for an alias,
  or `null` if the alias isn't mapped.
- `getAlias(string $class): ?string` — the alias for a class, or `null` if the
  class isn't mapped.

