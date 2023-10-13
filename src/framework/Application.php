<?php

namespace framework;

use ErrorException;
use framework\Http\View;

class Application
{
    private $config = [];

    public function __construct()
    {
        $this->startup();
    }

    private function startup()
    {
    }

    public function boot()
    {
    }
}
