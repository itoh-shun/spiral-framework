<?php

namespace Command\Commands\UseCases;

use Exception;

class CreateProjectDirectory {

    public static function execute($inputdata)
    {
        mkdir("src/$inputdata->projectName", 0700, true);
        mkdir("src/$inputdata->projectName/Application/Api", 0700, true);
        mkdir("src/$inputdata->projectName/Application/Web", 0700, true);
        mkdir("src/$inputdata->projectName/Enterprise", 0700, true);
        mkdir("src/$inputdata->projectName/Exceptions", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/Controlers/Web", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/Controlers/Api", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/GateWays", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/GateWays/Repository", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/GateWays/Middleware", 0700, true);
        mkdir("src/$inputdata->projectName/InterfaceAdapters/Presenters", 0700, true);
        mkdir("src/$inputdata->projectName/resources/html", 0700, true);
        mkdir("src/$inputdata->projectName/resources/mail", 0700, true);
        mkdir("src/$inputdata->projectName/resources/template", 0700, true);
        mkdir("src/$inputdata->projectName/StartUp", 0700, true);
        mkdir("src/$inputdata->projectName/config", 0700, true);
    }
}