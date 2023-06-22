<?php

namespace Fnp\ElModule\Helpers;

use Illuminate\Support\Str;

trait ActionHelper
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
    public static function name($provider = null): string
    {
        if ( ! $provider) {
            return strtolower(str_replace('\\', '.', get_called_class()));
        }

        return strtolower(
            str_replace('\\', '.', get_called_class()) . '.' .
            ucfirst(Str::camel($provider))
        );
    }
}
