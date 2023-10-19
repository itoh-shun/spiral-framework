<?php

class Collection extends stdClass
{
    public function __construct(array $ary = [])
    {
        foreach ($ary as $key => $val) {
            if (is_array($val)) {
                $this->{$key} = new Collection($val);
            } else {
                $this->{$key} = $val;
            }
        }
    }

    public function set($key, $val)
    {
        $this->{$key} = $val;
    }

    public function all()
    {
        return (array) $this;
    }

    public function toArray()
    {
        $tmp = [];
        foreach ($this->all() as $key => $val) {
            if ($val instanceof Collection) {
                $tmp[$key] = $val->toArray();
            } else {
                $tmp[$key] = $val;
            }
        }
        return $tmp;
    }

    public function count()
    {
        return count($this->toArray());
    }

    public function sum()
    {
        $sum_tmp = 0;
        foreach ($this->all() as $key => $val) {
            if (!is_numeric($val)) {
                throw new Exception('not numeric');
            }
            $sum_tmp = $sum_tmp + (float) $val;
        }
        return $sum_tmp;
    }

    public function avg()
    {
        return $this->sum() / $this->count();
    }

    public function max()
    {
        return max($this->all());
    }

    public function min()
    {
        return min($this->all());
    }

    public function where($key, $val)
    {
        $tmp = [];
        foreach ($this->toArray() as $ary_key => $ary) {
            if (isset($ary[$key]) && $ary[$key] === $val) {
                $tmp[] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function whereNot($key, $val)
    {
        $tmp = [];
        foreach ($this->toArray() as $ary_key => $ary) {
            if (isset($ary[$key]) && $ary[$key] !== $val) {
                $tmp[] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function whereIn($key, $val_in)
    {
        $tmp = [];
        foreach ($this->toArray() as $ary_key => $ary) {
            if (isset($ary[$key]) && in_array($ary[$key], $val_in)) {
                $tmp[] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function whereNotIn($key, $val_in)
    {
        $tmp = [];
        foreach ($this->toArray() as $ary_key => $ary) {
            if (isset($ary[$key]) && !in_array($ary[$key], $val_in)) {
                $tmp[] = $ary;
            }
        }
        return new Collection($tmp);
    }
    public function filter(callable $filter)
    {
        $tmp = [];
        foreach ($this->all() as $key => $val) {
            if ($filter($val, $key)) {
                $tmp[$key] = $val;
            }
        }
        return new Collection($tmp);
    }

    public function reject(callable $filter)
    {
        $tmp = [];
        foreach ($this->all() as $key => $val) {
            if (!$filter($val, $key)) {
                $tmp[$key] = $val;
            }
        }
        return new Collection($tmp);
    }

    public function first()
    {
        return $this->get(array_key_first($this->all()));
    }

    public function last()
    {
        return $this->get(array_key_last($this->all()));
    }

    public function get($index)
    {
        if (array_key_exists($index, $this->all())) {
            return $this->all()[$index];
        }
        return null;
    }

    public function column($key)
    {
        $result = [];
        foreach ($this->all() as $a) {
            $result[] = $a->{$key};
        }
        return $result;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function remove($name)
    {
        $old = $this->{$name};
        unset($this->{$name});
        return $old;
    }
}
