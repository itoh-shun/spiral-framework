<?php
echo '<?php

namespace '.$projectName.'\Exceptions;

use Exception;
use framework\Exception\ExceptionHandler as BaseExceptionHandler;
use framework\Http\View;

class ExceptionHandler extends BaseExceptionHandler {
    public $debug = false;

    public function render($request, Exception $exception)
    {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $title = "Error";
        echo View::forge("framework/resources/error",compact("code","message","title"))->render(true);
    }
}
';