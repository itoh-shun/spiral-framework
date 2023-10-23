<?php

// ビューの生成

namespace framework\Http;

use Collection\Collection;
use stdClass;

class View
{
    protected $file = null;
    public $data = [];
    public static $convertSetting = [];

    public function __construct(
        string $file = null,
        array $data = [],
        bool $filter = true
    ) {
        $this->file = $file;
        foreach ($data as $key => $d) {
            if ($d instanceof View) {
                $d = $d->render();
            }

            if ($filter) {
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public static function forge(
        string $file = null,
        array $data = [],
        bool $filter = true
    ): View {
        return new View($file, $data, $filter);
    }

    public function set_filename(string $file = null): void
    {
        $this->file = $file;
    }

    public function get(string $key = null, string $default = null): mixed
    {
        if ($key == null) {
            return $this->data;
        }

        return $this->data[$key] ? $this->data[$key] : $default;
    }

    public function set(
        string $key,
        string $value = null,
        bool $filter = true
    ): void {
        if ($filter) {
            $value = $this->filter($value);
        }
        $this->data[$key] = $value;
    }

    public function add_values(array $data, bool $filter = true): void
    {
        foreach ($data as $key => $d) {
            if ($d instanceof View) {
                $d = $d->render();
            }
            if ($filter) {
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public function filter($value)
    {
        if (!is_object($value) && !is_array($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        } //PHPサーバーはUTF-8

        if ($value instanceof Collection) {
            $tmp = new stdClass();
            foreach ((array) $value as $k => $t) {
                $t = $this->filter($t);
                $tmp->{$k} = $t;
            }

            return $tmp;
        }

        if (is_array($value)) {
            $tmp = new stdClass();
            foreach ($value as $k => $t) {
                $t = $this->filter($t);
                $tmp->{$k} = $t;
            }
            return $tmp;
        }

        return $value;
    }

    public function render(bool $isFullPath = false): string
    {
        if (is_array($this->data)) {
            extract($this->data, EXTR_PREFIX_SAME, 't_');
        }

        ob_start(); //バッファ制御スタート

        if ($isFullPath || VIEW_FILE_ROOT == '') {
            require $this->file . '.php';
        } else {
            require VIEW_FILE_ROOT . '/' . $this->file . '.php';
        }

        $html = ob_get_clean(); //バッファ制御終了＆変数を取得
        return $html;
    }

    public function h($value)
    {
        return $this->filter($value);
    }

    public function __toString()
    {
        return $this->render();
    }
}
