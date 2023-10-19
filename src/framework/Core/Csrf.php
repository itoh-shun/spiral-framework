<?php

use framework\Http\Session\Session;

class Csrf
{
    public static function generate($length = 16)
    {
        $string = Session::get('_csrf');


        if ($string == null) {
            while (($len = strlen($string)) < $length) {
                $size = $length - $len;
                $bytes = random_bytes($size);
                $string .= substr(
                    str_replace(['/', '+', '='], '', base64_encode($bytes)),
                    0,
                    $size
                );
            }
        }

        Session::put('_csrf', $string);

        return $string;
    }

    public static function validate($token, $throw = false)
    {
        $success = true;

        $string = Session::get('_csrf');

        $success = $string === $token;
        if (!$success && $throw) {
            throw new Exception('CSRF validation failed.', 300);
        }

        return $success;
    }
}
