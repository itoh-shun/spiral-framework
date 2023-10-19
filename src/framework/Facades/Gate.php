<?php

namespace framework\Facades;

use Auth;
use framework\Exception\NotFoundException;
use framework\Enterprise\CommonModels\GateInterface;

class Gate
{
    private static $gates = [];
    private static $auth;

    final public static function setAuth(Auth $auth)
    {
        self::$auth = $auth;
    }

    final public static function define(string $pass, $handler)
    {
        $gate = new GateDefine($pass, $handler);

        self::$gates[] = $gate;

        return $gate;
    }

    final public static function allows(string $pass, ...$instances)
    {
        $result = self::getGateInstance($pass, ...$instances);

        if ($result instanceof GateInterface) {
            return $result->can();
        }
        return $result;
    }

    final public static function denies(string $pass, ...$instances)
    {
        return !self::allows($pass, ...$instances);
    }

    final public static function getGateInstance(string $pass, ...$instances)
    {
        foreach (self::$gates as $gate) {
            if ($gate->processable($pass)) {
                if (self::$auth === null) {
                    self::$auth = auth();
                }
                return $gate->process(self::$auth, ...$instances);
            }
        }
        return false;
    }
}
