<?php

namespace Collection\Traits;

trait PropertyAccessTrait {
    /**
     * Set a value for a given key.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value) {
        $this->items[$key] = $value;
    }
    
    /**
     * Set a value for a given key.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value) {
        $this->__set($key , $value);
    }

    /**
     * Dynamically access collection items as object properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key) {
        return $this->items[$key] ?? null;
    }

    /**
     * Dynamically access collection items as object properties.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key) {
        return $this->__get($key);
    }

    /**
     * Remove a value for a given key.
     *
     * @param string $key
     * @return mixed
     */
    public function remove(string $key) {
        $oldValue = $this->items[$key] ?? null;
        unset($this->items[$key]);
        return $oldValue;
    }
}