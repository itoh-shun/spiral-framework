<?php

namespace Command\Commands\UseCases;

use Command\Basis\Core\View;
use Exception;

class CreateProjectDefaultFiles {

    public static function execute($inputdata)
    {
        $dir = ['Application' , 'Enterprise' , 'InterfaceAdapters' ,'Exceptions' ];
        $projectName = $inputdata->projectName;
        $text = View::forge('Command/Samples/makeAutoload',compact('projectName','dir'));
        file_put_contents("src/{$projectName}/makeAutoload.php", $text);
        
        $text = View::forge('Command/Samples/StartUp/web',compact('projectName'));
        file_put_contents("src/{$projectName}/StartUp/web.php", $text);
        
        $text = View::forge('Command/Samples/StartUp/api',compact('projectName'));
        file_put_contents("src/{$projectName}/StartUp/api.php", $text);

        $text = View::forge('Command/Samples/projectApplication',compact('projectName'));
        file_put_contents("src/{$projectName}/{$projectName}Application.php", $text);

        $text = View::forge('Command/Samples/config/app',compact('projectName'));
        file_put_contents("src/{$projectName}/config/app.php", $text);

        $text = View::forge('Command/Samples/Exceptions/ExceptionHandler',compact('projectName'));
        file_put_contents("src/{$projectName}/Exceptions/ExceptionHandler.php", $text);
        
        $text = View::forge('Command/Samples/InterfaceAdapters/Controlers/WelcomeController',compact('projectName'));
        file_put_contents("src/{$projectName}/InterfaceAdapters/Controlers/Web/WelcomeController.php", $text);

        $text = View::forge('Command/Samples/resources/template/base.blade');
        file_put_contents("src/{$projectName}/resources/template/base.blade.php", $text);

        $text = View::forge('Command/Samples/resources/html/welcome.blade');
        file_put_contents("src/{$projectName}/resources/html/welcome.blade.php", $text);
    }
}