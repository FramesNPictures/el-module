<?php

namespace Fnp\ElModule\Services;

use Fnp\ElModule\ElModule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
                $initMethod = static::methodExists($moduleProvider, 'init', $moduleGroup, 'OnDemand');

                if (!$initMethod) {
                    continue;
                }

                $this->application->call([$moduleProvider, $initMethod]);
            }
        }
    }

    /**
     * Builds a camelCased method name out of a prefix, a name and an
     * optional suffix (e.g. ('boot', 'ModuleRoutesWeb', 'Feature') becomes
     * "bootModuleRoutesWebFeature").
     *
     * @param  string|null  $prefix
     * @param  string  $name
     * @param  string|null  $suffix
     *
     * @return string
     */
    public static function methodName(?string $prefix, string $name, ?string $suffix = null): string
    {
        $name = str_replace([' ', '-', '.'], '_', $name);

        if (Str::contains($name, '_') || strtoupper($name) === $name) {
            $name = strtolower($name);
        }

        if (empty($prefix)) {
            $elPrefix = null;
            $elName   = Str::camel($name);
        } else {
            $elPrefix = $prefix;
            $elName   = ucfirst(Str::camel($name));
        }

        $elSuffix = $suffix ? ucfirst($suffix) : $suffix;

        return $elPrefix . $elName . $elSuffix;
    }

    /**
     * Returns the resolved method name when it exists on the given object,
     * otherwise null.
     *
     * @param  object|string  $object
     * @param  string|null  $prefix
     * @param  string  $name
     * @param  string|null  $suffix
     *
     * @return string|null
     */
    public static function methodExists($object, ?string $prefix, string $name, ?string $suffix = null): ?string
    {
        $method = static::methodName($prefix, $name, $suffix);

        return method_exists($object, $method) ? $method : null;
    }
}
