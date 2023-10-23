<?php
namespace Collection\Traits;

use Collection\Collection;
trait SearchTrait {
    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function search($value) {
        return array_search($value, $this->items);
    }

    /**
     * Get the first item from the collection.
     *
     * @param  callable|null  $callback
     * @return mixed
     */
    public function first(callable $callback = null) {
        if (is_null($callback)) {
            return reset($this->items);
        }
        
        foreach ($this->items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        
        return null;
    }

    /**
     * Get the last item from the collection.
     *
     * @param  callable|null  $callback
     * @return mixed
     */
    public function last(callable $callback = null) {
        if (is_null($callback)) {
            return end($this->items);
        }
        
        $reversedItems = array_reverse($this->items, true);
        
        foreach ($reversedItems as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        
        return null;
    }

    /**
     * Get the values from a single key in the collection.
     *
     * @param string $key
     * @return array
     */
    public function column(string $key): array {
        return array_column($this->items, $key);
    }
    
    /**
     * Get the maximum value from the collection.
     *
     * @return mixed
     */
    public function max() {
        return max($this->items);
    }

    /**
     * Get the minimum value from the collection.
     *
     * @return mixed
     */
    public function min() {
        return min($this->items);
    }
}