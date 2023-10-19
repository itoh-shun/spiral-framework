<?php

namespace framework\Exception;

use Exception;
use framework\Http\View;

class ExceptionHandler
{
    public $debug = false;

    public function render($request, Exception $exception)
    {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $title = 'Error';
        echo View::forge(
            'framework/resources/error',
            compact('code', 'message', 'title')
        )->render(true);
    }
}
