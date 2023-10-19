<?php

namespace framework\Http\Middleware;

use framework\Http\Request;
use framework\Support\ServiceProvider;

/**
 * Interface Middleware
 */
class Middleware
{
    protected Request $request;
    protected ServiceProvider $serviceProvider;

    public function __construct(Request $request, ServiceProvider $serviceProvider)
    {
        $this->request = $request;
        $this->serviceProvider = $serviceProvider;
    }
}
