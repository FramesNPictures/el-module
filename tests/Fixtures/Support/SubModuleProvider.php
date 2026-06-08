<?php

namespace Fnp\ElModule\Tests\Fixtures\Support;

use Illuminate\Support\ServiceProvider;

class SubModuleProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance('submodule.registered', true);
    }
}
