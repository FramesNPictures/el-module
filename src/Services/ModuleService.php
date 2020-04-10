<?php

namespace Fnp\Module\Services;

use Fnp\Dto\Common\Helper\Obj;
use Fnp\Module\ModuleProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class ModuleService
{
    /**
     * @return Collection
     * @throws \ReflectionException
     */
    public function getModuleProviders()
    {
        $modules = new Collection();

        foreach ($this->getServiceProviders() as $provider)
            if ($provider instanceof ModuleProvider)
                $modules->push($provider);

        return $modules;
    }

    /**
     * @return Collection
     * @throws \ReflectionException
     */
    public function getServiceProviders()
    {
        $app = new \ReflectionClass(Application::getInstance());
        $pro = $app->getProperty('serviceProviders');
        $pro->setAccessible(TRUE);
        $val = $pro->getValue(Application::getInstance());

        return new Collection($val);
    }

    /**
     * @param $moduleGroups
     */
    public function initOnDemand($moduleGroups)
    {
        if (!is_array($moduleGroups))
            $moduleGroups = [$moduleGroups];

        foreach ($this->getModuleProviders() as $moduleProvider)
            foreach ($moduleGroups as $moduleGroup) {
                $initMethod = Obj::methodExists($moduleProvider, 'init', $moduleGroup, 'OnDemand');

                if (!$initMethod)
                    continue;

                $moduleProvider->$initMethod();
            }
    }
}