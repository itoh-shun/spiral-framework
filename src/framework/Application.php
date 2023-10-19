<?php

namespace framework;

use ErrorException;
use framework\Http\View;
use framework\Support\ServiceProvider;

class Application
{
    private $config = [];

    public ServiceProvider $serviceProvider;

    public function __construct()
    {
        $this->startup();
        $this->serviceProvider = new ServiceProvider();
    }

    private function startup()
    {
    }

    public function boot()
    {
    }
}
