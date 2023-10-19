<?php

namespace framework\Library;

use framework\SpiralConnecter\Paginator;
use framework\SpiralConnecter\SpiralDB;
use framework\SpiralConnecter\SpiralManager;

interface SiRule
{
    public function processable($value);
    public function message();
    public function name();
}

class ArrayRule implements SiRule
{
    private string $name = 'arrayRule';
    private array $values = [];

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public static function in($values)
    {
        return new self($values);
    }

    public function processable($value)
    {
        return in_array($value, $this->values);
    }

    public function message()
    {
        return [
            'ja' => [
                $this->name => '{field}の内容が不正です',
            ],
        ];
    }

    public function name()
    {
        return $this->name;
    }
}

class SpiralDbUniqueRule implements SiRule
{
    private string $name = 'spiralDbUnique';
    private string $uniqueKey = '';
    private SpiralManager $table;

    public function __construct(SpiralManager $table, $uniqueKey)
    {
        $this->table = $table;
        $this->uniqueKey = $uniqueKey;
    }

    public static function unique(
        $tableName,
        $uniqueKey,
        ?callable $searchCallable = null
    ) {
        $instance = SpiralDB::title($tableName);
        if (is_callable($searchCallable)) {
            $instance = $searchCallable($instance);
        }
        return new self($instance, $uniqueKey);
    }

    public function processable($value)
    {
        $result = $this->table->where($this->uniqueKey, $value)->paginate(1);
        if ($result instanceof Paginator && $result->getTotal() == 0) {
            return true;
        }
        return false;
    }

    public function message()
    {
        return [
            'ja' => [
                $this->name => '{field}は重複しています',
            ],
        ];
    }

    public function name()
    {
        return $this->name;
    }
}

class SpiralDbExistRule implements SiRule
{
    private string $name = 'spiralDbExist';
    private string $searchKey = '';
    private SpiralManager $table;

    public function __construct(SpiralManager $table, $searchKey)
    {
        $this->table = $table;
        $this->searchKey = $searchKey;
    }

    public static function exists(
        $tableName,
        $searchKey,
        ?callable $searchCallable = null
    ) {
        $instance = SpiralDB::title($tableName);
        if (is_callable($searchCallable)) {
            $instance = $searchCallable($instance);
        }
        return new self($instance, $searchKey);
    }

    public function processable($value)
    {
        $result = $this->table->where($this->searchKey, $value)->paginate(1);
        if ($result instanceof Paginator && $result->getTotal() == 0) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return [
            'ja' => [
                $this->name => '{field}は存在しません',
            ],
        ];
    }

    public function name()
    {
        return $this->name;
    }
}
