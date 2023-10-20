<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Command\Commands\Interactor\CreateProjectInteractorInputData;
use Command\Commands\Interactor\CreateProjectInteractorInputPortInterface;

class ApplicationInitalize extends Command {

    public string $serialize = "app:init";
    private CreateProjectInteractorInputPortInterface $inputPort;
    
    public function __construct(CreateProjectInteractorInputPortInterface $createProjectInteractor)
    {
        parent::__construct();
        $this->inputPort = $createProjectInteractor;
    }

    protected function defineOptions()
    {
        $this->addOption('n', 'name', 'Specify the project name' , true);
    }
    
    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');
        
        // Check if the name option is set, otherwise ask for it
        $projectName = $this->getOptionValue('name');
        if (!$projectName) {
            $projectName = $this->ask("Please specify project name: ");
        }

        $inputData = new CreateProjectInteractorInputData(['projectName' => $projectName]);
        $this->inputPort->execute($inputData);

        if (!file_exists("composer.json")) {
            exec("composer init");
        }
    }
}