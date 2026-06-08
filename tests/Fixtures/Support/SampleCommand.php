<?php

namespace Fnp\ElModule\Tests\Fixtures\Support;

use Illuminate\Console\Command;

class SampleCommand extends Command
{
    protected $signature = 'el-module:sample';

    protected $description = 'A sample command used by the el-module test suite.';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
