<?php

namespace framework\Facades;

use Auth;
use framework\Exception\NotFoundException;

class GateDefine
{
    private $pass;
    private $handler;
    private $action;

    public function __construct(string $pass, $handler)
    {
        $this->pass = $pass;
        if (is_array($handler)) {
            $this->handler = $handler[0];
            $this->action = $handler[1];
        } else {
            $this->handler = $handler;
        }
    }

    final public function processable(string $pass): bool
    {
        if ($pass !== $this->pass) {
            return false;
        }

        return true;
    }

    final public function process(Auth $auth, ...$instances)
    {
        $handler = $this->handler;

        if (!is_string($handler)) {
            return $handler($auth, ...$instances);
        }

        if (!is_string($this->action)) {
            return false;
        }

        $action = $this->action;
        //__constract Method実行
        $instance = new $handler();
        return $instance->$action($auth, ...$instances);
    }
}
