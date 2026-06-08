<?php

namespace Fnp\ElModule\Tests\Fixtures\Support;

class EnglishGreeter implements GreeterInterface
{
    public function greet(): string
    {
        return 'hello';
    }
}
