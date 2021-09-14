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
        return str_replace('\\','',get_called_class());
    }
}