<?php

namespace Command\Commands\Interactor;

use Command\Commands\UseCases\CreateProjectDefaultFiles;
use Command\Commands\UseCases\CreateProjectDirectory;
use Exception;
use stdClass;

class CreateProjectInteractor implements CreateProjectInteractorInputPortInterface {

    public function execute(CreateProjectInteractorInputData $inputdata)
    {

        if(file_exists('src/'.$inputdata->projectName))
        {
            throw new Exception('There is already a directory.',1);
        }

        CreateProjectDirectory::execute($inputdata);
        CreateProjectDefaultFiles::execute($inputdata);


    }
}


class CreateProjectInteractorInputData {
    public function __construct(array $inputData)
    {
        $this->projectName = $inputData['projectName'];
    }
}

interface CreateProjectInteractorInputPortInterface {

    public function execute(CreateProjectInteractorInputData $inputdata);
}