<?php

namespace Collection\Traits;

use Collection\Collection;

trait ManipulationTrait {
    /**
     * Prepend an item to the beginning of the collection.
     *
     * @param  mixed  $value
     * @param  mixed  $key
     * @return static
     */
    public function prepend($value, $key = null): Collection {
        if (is_null($key)) {
            array_unshift($this->items, $value);
        } else {
            $this->items = [$key => $value] + $this->items;
        }
        
        return $this;
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle(): Collection {
        $items = $this->items;
        shuffle($items);
        /** @phpstan-ignore-next-line */
        return new static($items);
    }

    /**
     * Reverse the items in the collection.
     *
     * @return static
     */
    public function reverse(): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_reverse($this->items));
    }
}