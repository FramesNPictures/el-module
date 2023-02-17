<?php

namespace Fnp\ElModule\Services;

use Fnp\ElHelper\Obj;
use Fnp\ElModule\ElModule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;

class ElModuleService
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * ElModuleService constructor.
     *
     * @param  Application  $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Collection|ElModule[]
     * @throws ReflectionException
     */
    public function getModuleProviders()
    {
        $modules = new Collection();

        foreach ($this->getServiceProviders() as $provider) {
            if ($provider instanceof ElModule) {
                $modules->push($provider);
            }
        }

        return $modules;
    }

    /**
     * @return Collection
     * @throws ReflectionException
     */
    public function getServiceProviders()
    {
        $app = new ReflectionClass($this->application);
        $pro = $app->getProperty('serviceProviders');
        $pro->setAccessible(true);
        $val = $pro->getValue($this->application);

        return new Collection($val);
    }

    /**
     * Initialize on demand feature
     *
     * @param $moduleGroups
     * @throws ReflectionException
     */
    public function initOnDemand($moduleGroups)
    {
        if (!is_array($moduleGroups)) {
            $moduleGroups = [$moduleGroups];
        }

        foreach ($this->getModuleProviders() as $moduleProvider) {
            foreach ($moduleGroups as $moduleGroup) {
                $initMethod = Obj::methodExists($moduleProvider, 'init', $moduleGroup, 'OnDemand');

                if (!$initMethod) {
                    continue;
                }

                $this->application->call([$moduleProvider, $initMethod]);
            }
        }
    }
}
