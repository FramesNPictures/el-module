<?php

namespace Fnp\ElModule\Tests\Fixtures\Support;

use Illuminate\Contracts\Foundation\Application;

/**
 * A fake feature trait. Its hook methods follow the same
 * init/register/boot<Trait>Feature naming convention real features use, so
 * exercising it proves the ElModule discovery/dispatch mechanism itself.
 */
trait Spy
{
    /** @var string[] Ordered record of the lifecycle phases that fired. */
    public array $calls = [];

    /** The Application instance the container injected into the init hook. */
    public ?Application $injectedApp = null;

    public function initSpyFeature(Application $application): void
    {
        $this->calls[]     = 'init';
        $this->injectedApp = $application;
    }

    public function registerSpyFeature(): void
    {
        $this->calls[] = 'register';
    }

    public function bootSpyFeature(): void
    {
        $this->calls[] = 'boot';
    }

    public function initReportsOnDemand(): void
    {
        $this->calls[] = 'ondemand:reports';
    }
}
