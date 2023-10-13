<?php
/**
* php spiralframe app:init JoyPla
* php spiralframe controller:make User
* php spiralframe usecase:make User Index
* php spiralframe repository:make User
* php spiralframe presenter:make User Index
* php spiralframe model:make User spiraldbtitle
*/

namespace Clarc\Basis\Console;


use Clarc\Basis\Console\Command\Make\ClarcMakeCommand;
use Clarc\Basis\Console\Input\StreamInput;
use Clarc\Basis\Console\Output\StreamOutput;
use Command\Basis\Request\CommandArgv;
use Command\Basis\Request\Commander;
use Exception;

class Kernel
{
    private $makeCommand;
    private CommandArgv $commandArgv;
    private Commander $commander;

    public function __construct(Commander $commander)
    {
        $this->commander = $commander;
    }

    public function handle(CommandArgv $commandArgv)
    {
        $command = $this->commander->dispatch($commandArgv);
        if(is_null($command))
        {
            echo $this->commander->helper();
            return ;
        }

        try{
            $command->execute($commandArgv);
        } catch ( Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}