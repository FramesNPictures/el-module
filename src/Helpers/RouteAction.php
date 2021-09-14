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
    abstract public static function name(): string;
}