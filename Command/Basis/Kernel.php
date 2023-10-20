<?php

namespace Clarc\Basis\Console;

use Clarc\Basis\Console\Command\Make\ClarcMakeCommand;
use Clarc\Basis\Console\Input\StreamInput;
use Clarc\Basis\Console\Output\StreamOutput;
use Command\Basis\Request\CommandArgv;
use Command\Basis\Request\Commander;
use Exception;

class Kernel
{
    private $commander;

    public function __construct(Commander $commander)
    {
        $this->commander = $commander;
    }

    public function handle(CommandArgv $commandArgv)
    {
        
        $command = $this->commander->dispatch($commandArgv);
        if (is_null($command)) {
            $this->commander->helper();
            return;
        }

        // Check for help options
        if (in_array('-h', $commandArgv->options) || in_array('--help', $commandArgv->options)) {
            $command->displayHelp();
            return;
        }

        // Parse options before executing the command
        $command->parseOptions($commandArgv);
        try {
            $command->execute($commandArgv);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    private function displayHelp()
    {
        echo "使用方法:\n";
        echo "php spiralframe [command] [options]\n\n";
        echo "利用可能なコマンド:\n";
        $this->commander->helper();
        echo "\nオプション:\n";
        echo "-h, --help    ヘルプを表示\n";
    }
}