<?php

use framework\Http\Session\Session;
use framework\Library\BladeLikeEngine\BladeOneCustom;

class BladeOneCsrf
{
    public static function validate($throw = false)
    {
        $blade = new BladeOneCustom();
        $success = $blade->csrfIsValid();
        if ($success !== true && $throw) {
            throw new Exception('CSRF validation failed.', 300);
        }

        return $success;
    }
}
