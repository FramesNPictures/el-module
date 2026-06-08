<?php

namespace Fnp\ElModule\Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // The morph map is global static state shared across the process,
        // so reset it before every test to keep them isolated.
        Relation::morphMap([], false);
        Relation::requireMorphMap(false);
    }

    protected function tearDown(): void
    {
        Relation::morphMap([], false);
        Relation::requireMorphMap(false);

        parent::tearDown();
    }
}
