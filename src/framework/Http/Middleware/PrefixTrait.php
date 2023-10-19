<?php

namespace framework\Http\Middleware;

/**
 * Trait PrefixTrait
 *
 */
trait PrefixTrait
{
    private static string $prefix = "";

    final public static function prefix($prefix, callable $func)
    {
        $before = self::$prefix;
        self::$prefix = (self::$prefix == '') ? $prefix : self::$prefix.'/'.$prefix;
        $func();
        self::$prefix = $before;
    }
}
