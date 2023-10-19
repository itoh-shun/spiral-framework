<?php

namespace framework\Support;

use Closure;
use Exception;

class ServiceProvider
{
    private $definitions = [];
    private $instances = [];

    // サービスを登録します
    public function register($name, $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    // サービスを取得します
    public function get($name)
    {
        // インスタンスが既に存在する場合はそれを返す
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (!isset($this->definitions[$name])) {
            throw new Exception("Service not found: " . $name);
        }

        // サービス定義を元にインスタンスを生成
        $definition = $this->definitions[$name];
        if ($definition instanceof Closure) {
            $this->instances[$name] = $definition($this);
        } else {
            $this->instances[$name] = $definition;
        }

        return $this->instances[$name];
    }
}
