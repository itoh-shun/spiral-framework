<?php
echo '<?php

namespace '.$projectName.'\InterfaceAdapters\Controllers\Web ;

use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;

class WelcomeController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(array $vars)
    {
        echo view("html/welcome")->render();
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
