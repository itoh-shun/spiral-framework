<?php
namespace Command\Basis\Request;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;

class Commander {

    private array $commands = [];

    public static function init()
    {
        return new self();
    }

    public function add(Command $command)
    {
        $this->commands[] = $command;
    }

    public function dispatch(CommandArgv $commandArgv)
    {
        foreach($this->commands as $command)
        {
            if($command->getSerialize() === $commandArgv->getSerialize())
            {
                return $command;
            }
        }

        return null;
    }

    public function helper()
    {
        $this->message("Available command list");
        foreach($this->commands as $command)
        {
            $this->message($command->getSerialize());
        }
    }
    
    private static function message(string $message)
    {
        echo escapeshellcmd($message).PHP_EOL;
    }
}