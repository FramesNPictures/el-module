<?php

namespace Fnp\ElModule\Helpers;

use Illuminate\Database\Eloquent\Relations\Relation;

class HClassMap
{
    /**
     * Resolve a registered alias to its fully qualified class name.
     *
     * @param  string  $alias  Alias to resolve
     *
     * @return string|null  Class name or null when the alias is not mapped
     */
    public static function getClass(string $alias): ?string
    {
        return Relation::getMorphedModel($alias);
    }

    /**
     * Resolve a class name to its registered alias.
     *
     * @param  string  $class  Fully qualified class name to resolve
     *
     * @return string|null  Alias or null when the class is not mapped
     */
    public static function getAlias(string $class): ?string
    {
        $alias = array_search($class, Relation::morphMap(), true);

        return $alias === false ? null : $alias;
    }
}
