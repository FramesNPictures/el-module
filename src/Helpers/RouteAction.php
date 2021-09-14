<?php

namespace Fnp\ElModule\Helpers;

trait RouteAction
{
    /**
     * @param  string  $provider
     *
     * @return array
     */
    public static function action(string $provider = 'handle'): array
    {
        return [get_called_class(), $provider];
    }

    /**
     *
     */
    public static function name(): string
    {
        $elements = explode('\\',get_called_class());
        return array_pop($elements);
    }
}