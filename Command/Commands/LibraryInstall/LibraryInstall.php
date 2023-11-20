<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Command\Commands\Interactor\LibraryInstallInteractorInputData;
use Command\Commands\Interactor\LibraryInstallInteractorInputPortInterface;

class LibraryInstall extends Command {

    public string $serialize = "library:install";
    private LibraryInstallInteractorInputPortInterface $inputPort;
    
    public function __construct(LibraryInstallInteractorInputPortInterface $libraryInstallInteractor)
    {
        parent::__construct();
        $this->inputPort = $libraryInstallInteractor;
    }

    protected function defineOptions()
    {
        $this->addOption('n', 'name', 'library name' , true);
    }
    
    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');

        $list = require __DIR__."/librarylist.php";
        
        // Check if the name option is set, otherwise ask for it
        $name = $this->getOptionValue('name');
        if (empty($name) || empty($list[$name])) {
            $this->line("ライブラリが存在しません");
            $this->line('利用可能なライブラリ:');

            foreach($list as $k => $l){
                $this->line($k);
            }

            throw new \Exception('', 1);
        }

        $inputData = new LibraryInstallInteractorInputData(['url' => $list[$name]]);
        $this->inputPort->execute($inputData);
    }
}