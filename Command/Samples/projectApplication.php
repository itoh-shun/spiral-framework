<?php
echo '<?php

namespace '.$projectName.';

use framework\Application;

class '.$projectName.'Application extends Application
{
    public function __construct()
    {
        config_path("'.$projectName.'/config/app");
        parent::__construct();
    }

    public function boot()
    {
    }
}
';