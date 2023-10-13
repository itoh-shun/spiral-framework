<?php

namespace framework\Http\Middleware;

use framework\Http\Request;

/**
 * Interface Middleware
 */
class Middleware
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
