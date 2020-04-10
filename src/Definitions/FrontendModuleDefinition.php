<?php

namespace Fnp\Module\Definitions;

use Fnp\Dto\Common\Helper\Obj;
use Illuminate\Support\Facades\Config;

class FrontendModuleDefinition
{
    protected $handle = 'app';

    /**
     * @var string
     */
    protected $vue = NULL;

    /**
     * @var array
     */
    protected $vueData = [];

    /**
     * @var array
     */
    protected $vueComponents = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var array
     */
    protected $sass = [];

    /**
     * @var array
     */
    protected $less = [];

    /**
     * @var array
     */
    protected $images = [];


    public function merge(FrontendModuleDefinition $definition)
    {
        if (!$this->getVue() && $definition->getVue())
            $this->setVue($definition->getVue());

        foreach ($definition->getVueComponents() as $key => $value)
            $this->addVueComponent($key, $value);

        foreach ($definition->getCss() as $path)
            $this->addCss($path);

        foreach ($definition->getSass() as $path)
            $this->addSass($path);

        foreach ($definition->getLess() as $path)
            $this->addLess($path);

        foreach ($definition->getJs() as $path)
            $this->addJs($path);

        foreach ($definition->getImages() as $key => $path)
            $this->addImage($key, $path);

        foreach ($definition->getVueData() as $key => $value)
            $this->addVueData($key, $value);
    }

    public function setHandle($handle): FrontendModuleDefinition
    {
        $this->handle = $handle;

        return $this;
    }

    public function addVueData($key, $value = NULL): FrontendModuleDefinition
    {
        if (!$this->isVue())
            $this->setVue('#app');

        $this->vueData[ $key ] = $value;

        return $this;
    }

    public function addVueComponent($name, $path): FrontendModuleDefinition
    {
        if (!$this->isVue())
            $this->setVue('#app');

        $this->vueComponents[ $name ] = $path;

        return $this;
    }

    public function addJs($path): FrontendModuleDefinition
    {
        $this->js[] = $path;

        return $this;
    }

    public function addCss($path): FrontendModuleDefinition
    {
        $this->css[] = $path;

        return $this;
    }

    public function addSass($path): FrontendModuleDefinition
    {
        $this->sass[] = $path;

        return $this;
    }

    public function addLess($path): FrontendModuleDefinition
    {
        $this->less[] = $path;

        return $this;
    }

    public function addImage($key, $path): FrontendModuleDefinition
    {
        $this->images[ $key ] = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function getVueData(): array
    {
        return $this->vueData;
    }

    /**
     * @param bool $relative
     *
     * @return mixed
     */
    public function getVueComponents()
    {
        return $this->vueComponents;
    }

    /**
     * @return string
     */
    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * @return array
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * @return array
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * @return array
     */
    public function getSass(): array
    {
        return $this->sass;
    }

    /**
     * @return array
     */
    public function getLess(): array
    {
        return $this->less;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getModuleFileName($extension): string
    {
        return $this->getHandle() . '.' . $extension;
    }

    public function getModuleMethodName($prefix, $suffix): string
    {
        return Obj::methodName($prefix, $this->getHandle(), $suffix);
    }

    public function getTargetModuleFilePath($extension): string
    {
        return Config::get('module.path') .
               '/' . $this->getModuleFileName($extension);
    }

    public function getRelativeTargetModuleFilePath($extension): string
    {
        return '.' . str_replace(base_path(), '', $this->getTargetModuleFilePath($extension));
    }

    public function relative($filename): string
    {
        $rel = str_replace(base_path(), '', $filename);
        $up  = str_replace(base_path(), '', Config::get('module.path'));
        $up  = explode('/', $up);
        $up  = str_repeat('../', count($up) - 1);

        return str_replace('//', '/', $up . $rel);
    }

    /**
     * @return string
     */
    public function getVue()
    {
        return $this->vue;
    }

    public function isVue()
    {
        return !is_null($this->vue);
    }

    /**
     * @param string $vue
     *
     * @return FrontendModuleDefinition
     */
    public function setVue(string $element): FrontendModuleDefinition
    {
        $this->vue = $element;

        return $this;
    }
}