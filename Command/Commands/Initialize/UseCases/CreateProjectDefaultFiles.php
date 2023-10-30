<?php

namespace Command\Commands\UseCases;

use Command\Basis\Core\View;
use Exception;

class CreateProjectDefaultFiles {

    public static function execute($inputdata)
    {
        $dir = ['Application' , 'Enterprise' , 'InterfaceAdapters' ,'Exceptions' ];
        $projectName = $inputdata->projectName;
        $text = View::forge('spiral-framework/Command/Samples/makeAutoload',compact('projectName','dir'));
        file_put_contents("src/{$projectName}/makeAutoload.php", $text);
        
        $text = View::forge('spiral-framework/Command/Samples/routes/batch',compact('projectName'));
        file_put_contents("src/{$projectName}/routes/batch.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/routes/web',compact('projectName'));
        file_put_contents("src/{$projectName}/routes/web.php", $text);
        
        $text = View::forge('spiral-framework/Command/Samples/routes/api',compact('projectName'));
        file_put_contents("src/{$projectName}/routes/api.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/projectApplication',compact('projectName'));
        file_put_contents("src/{$projectName}/{$projectName}Application.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/config/app',compact('projectName'));
        file_put_contents("src/{$projectName}/config/app.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/app/Exceptions/ExceptionHandler',compact('projectName'));
        file_put_contents("src/{$projectName}/app/Exceptions/ExceptionHandler.php", $text);
        
        $text = View::forge('spiral-framework/Command/Samples/app/Http/Controllers/WelcomeController',compact('projectName'));
        file_put_contents("src/{$projectName}/app/Http/Controllers/Web/WelcomeController.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/resources/template/base.blade');
        file_put_contents("src/{$projectName}/resources/template/base.blade.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/resources/html/welcome.blade');
        file_put_contents("src/{$projectName}/resources/html/welcome.blade.php", $text);

        $text = View::forge('spiral-framework/Command/Samples/resources/error.blade');
        file_put_contents("src/{$projectName}/resources/error.blade.php", $text);
    }
}