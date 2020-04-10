<?php

namespace Fnp\ElModule\Definitions;

class PhpUnitDefinition
{
    protected $testSuites = [];

    /**
     * @return array
     */
    public function getTestSuites(): array
    {
        return $this->testSuites;
    }

    public function addTestSuitePath($suite, $path)
    {
        $this->testSuites[ $suite ][] = $path;

        return $this;
    }
}