<?php
echo '<?php

namespace '.$projectName.'\App\Http\Controllers\\'.$type.' ;

use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;
use framework\Support\ServiceProvider;

class '.$model.'Controller extends Controller
{

    public function index(array $vars)
    {
        //
    }

    public function create(array $vars)
    {
        //
    }

    public function store(array $vars)
    {
        //
    }

    public function show(array $vars)
    {
        //
    }

    public function edit(array $vars)
    {
        //
    }

    public function update(array $vars)
    {
        //
    }

    public function destroy(array $vars)
    {
        //
    }
}
';