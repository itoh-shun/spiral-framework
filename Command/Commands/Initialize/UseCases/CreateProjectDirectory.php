<?php

namespace Command\Commands\UseCases;

use Exception;

class CreateProjectDirectory {

    public static function execute($inputdata)
    {
        mkdir("src/$inputdata->projectName", 0700, true);
        mkdir("src/$inputdata->projectName/app", 0700, true);
        mkdir("src/$inputdata->projectName/app/Http/Controllers/Web", 0700, true);
        mkdir("src/$inputdata->projectName/app/Http/Controllers/Api", 0700, true);
        mkdir("src/$inputdata->projectName/app/Http/Middleware", 0700, true);
        mkdir("src/$inputdata->projectName/app/Exceptions", 0700, true);
        mkdir("src/$inputdata->projectName/app/Providers", 0700, true);
        mkdir("src/$inputdata->projectName/resources/html", 0700, true);
        mkdir("src/$inputdata->projectName/resources/mail", 0700, true);
        mkdir("src/$inputdata->projectName/resources/template", 0700, true);
        mkdir("src/$inputdata->projectName/routes", 0700, true);
        mkdir("src/$inputdata->projectName/config", 0700, true);
    }
}