<?php

namespace Command\Basis\Request;

class CommandArgv {

    private string $serialize = '';
    private array $options = [];

    public function __construct($argv)
    {
        $this->serialize = $argv[1];
        
        unset($argv[0]);
        unset($argv[1]);
        
        $this->options = array_values($argv);
    }

    public function getSerialize()
    {
        return $this->serialize;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}