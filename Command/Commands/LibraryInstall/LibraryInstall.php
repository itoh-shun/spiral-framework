<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Command\Commands\Interactor\CreateProjectInteractorInputData;
use Command\Commands\Interactor\CreateProjectInteractorInputPortInterface;

class LibraryInstall extends Command {

    public string $serialize = "library:install";
    private CreateProjectInteractorInputPortInterface $inputPort;
    
    public function __construct(CreateProjectInteractorInputPortInterface $createProjectInteractor)
    {
        parent::__construct();
        $this->inputPort = $createProjectInteractor;
    }

    protected function defineOptions()
    {
        $this->addOption('n', 'name', 'library name' , true);
    }
    
    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');
        
        // Check if the name option is set, otherwise ask for it
        $name = $this->getOptionValue('name');
        if (!$name) {
            throw new \Exception('ライブラリ名を入力してください');
        }

        $inputData = new CreateProjectInteractorInputData(['name' => $name]);
        $this->inputPort->execute($inputData);
    }
}