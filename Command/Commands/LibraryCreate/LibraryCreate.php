<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Command\Commands\Interactor\LibraryCreateInteractorInputData;
use Command\Commands\Interactor\LibraryCreateInteractorInputPortInterface;

class LibraryCreate extends Command {

    public string $serialize = "library:create";
    private LibraryCreateInteractorInputPortInterface $inputPort;
    
    public function __construct(LibraryCreateInteractorInputPortInterface $createProjectInteractor)
    {
        parent::__construct();
        $this->inputPort = $createProjectInteractor;
    }

    protected function defineOptions()
    {
        $this->addOption('u', 'url', 'set remote url' , true);
    }
    
    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');
        
        // Check if the name option is set, otherwise ask for it
        $url = $this->getOptionValue('url');
        if (!$url) {
            throw new \Exception('ライブラリ名を入力してください');
        }

        $inputData = new LibraryCreateInteractorInputData(['url' => $url]);
        $this->inputPort->execute($inputData);
    }
}