<?php

namespace framework\Http\Session;

use Closure;

class Session
{
    public static function getId()
    {
        return session_id();
    }

    public static function generate()
    {
        session_start();
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function get(string $key, $any = null)
    {
        if (isset($_SESSION[$key])) {
            return Session::all()[$key];
        }

        if ($any instanceof Closure) {
            return $any();
        }

        return $any;
    }

    public static function put(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key)
    {
        return isset($_SESSION[$key]);
    }

    public static function exists(string $key)
    {
        return array_key_exists($key, self::all());
    }

    public static function missing(string $key)
    {
        return !isset($_SESSION[$key]);
    }

    public static function forget($key)
    {
        if (is_string($key)) {
            unset($_SESSION[$key]);
        }

        if (is_array($key)) {
            foreach ($key as $k) {
                self::forget($k);
            }
        }
    }

    public static function flush()
    {
        session_destroy();
    }
}

class RequestSession
{
    public function __construct()
    {
        Session::generate();
    }

    public function getId()
    {
        return Session::getId();
    }

    public function regenerate()
    {
        return Session::regenerate();
    }

    public function all()
    {
        return Session::all();
    }

    public function get(string $key, $any = null)
    {
        return Session::get($key, $any);
    }

    public function put(string $key, $value)
    {
        return Session::put($key, $value);
    }

    public function has(string $key)
    {
        return Session::has($key);
    }

    public function exists(string $key)
    {
        return Session::exists($key);
    }

    public function forget(string $key)
    {
        return Session::forget($key);
    }

    public static function missing(string $key)
    {
        return Session::missing($key);
    }

    public function flush()
    {
        return Session::flush();
    }
}
