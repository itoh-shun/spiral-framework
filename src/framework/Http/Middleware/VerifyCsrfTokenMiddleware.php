<?php

namespace framework\Http\Middleware;

use Csrf;

class VerifyCsrfTokenMiddleware extends Middleware implements
    MiddlewareInterface
{
    public function process(array $vars)
    {

        $token = $this->request->get('_csrf');
        return Csrf::validate($token, true);
    }
}
