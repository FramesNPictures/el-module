<?php

namespace Fnp\Module;

use Fnp\Dto\Common\Helper\Obj;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

abstract class ModuleProvider extends ServiceProvider
{
    protected $__bootFeatures     = [];
    protected $__registerFeatures = [];

    public function __construct($app)
    {
        parent::__construct($app);
        $this->initFeatures();
    }

    public function register()
    {
        $this->registerFeatures();
    }

    /**
     * Bootstrap services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot()
    {
        $this->bootFeatures();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected function initFeatures()
    {
        $class = static::class;

        $initialized = [];

        foreach (class_uses_recursive($class) as $trait) {

            $method = Obj::methodName('init', class_basename($trait), 'Feature');

            if (method_exists($class, $method) && !in_array($method, $initialized)) {
                App::call([$this, $method]);

                $initialized[] = $method;
            }

            $method = Obj::methodName('boot', class_basename($trait), 'Feature');

            if (method_exists($class, $method) && !in_array($method, $this->__bootFeatures))
                $this->__bootFeatures[] = $method;

            $method = Obj::methodName('register', class_basename($trait), 'Feature');

            if (method_exists($class, $method) && !in_array($method, $this->__registerFeatures))
                $this->__registerFeatures[] = $method;
        }
    }

    protected function bootFeatures()
    {
        foreach ($this->__bootFeatures as $feature)
            App::call([$this, $feature]);
    }

    protected function registerFeatures()
    {
        foreach ($this->__registerFeatures as $feature)
            App::call([$this, $feature]);
    }

}