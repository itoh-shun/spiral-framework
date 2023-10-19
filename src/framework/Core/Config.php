<?php

class Config
{
    private static string $path = '';
    private $config = [];

    public function __construct()
    {
        $this->setConfig();
    }

    public static function setPath($path)
    {
        self::$path = $path;
    }

    private function setConfig()
    {
        $this->config = require self::$path . '.php';
    }

    public function get(string $key, $default = '')
    {
        $keys = explode('.', $key);
        return $this->getValue($this->config, $keys, $default);
    }

    private function getValue(array $array, array $keys, $default = '')
    {
        if (!isset($array[$keys[0]])) {
            return $default;
        }

        if (is_array($array[$keys[0]]) && isset($keys[1])) {
            $val = $array[$keys[0]];
            unset($keys[0]);
            $keys = array_values($keys);
            return $this->getValue($val, $keys, $default);
        }

        if (!is_array($array[$keys[0]]) && isset($keys[1])) {
            return $default;
        }

        return $array[$keys[0]];
    }
}
