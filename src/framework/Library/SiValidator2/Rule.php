<?php

namespace SiValidator2;

use framework\SpiralConnecter\SpiralDB;
use SiValidator2\Rules\ExistsRule;
use SiValidator2\Rules\UniqueRule;

class Rule
{
    public static function exists(string $table, string $column = null)
    {
        return new ExistsRule($table, $column);
    }
    public static function unique(string $table, string $column = null)
    {
        return new UniqueRule($table, $column);
    }
}
