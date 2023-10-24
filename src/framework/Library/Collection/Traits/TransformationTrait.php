<?php
namespace Collection\Traits;

use Collection\Collection;
trait TransformationTrait {
    /**
     * Get the values of a single key from the collection.
     *
     * @param  string  $value
     * @param  string|null  $key
     * @return static
     */
    public function pluck(string $value, ?string $key = null): Collection {
        $items = [];
        
        foreach ($this->items as $item) {
            if (is_array($item) && isset($item[$value])) {
                if (is_null($key)) {
                    $items[] = $item[$value];
                } else {
                    $items[$item[$key]] = $item[$value];
                }
            }
        }
        /** @phpstan-ignore-next-line */
        return new static($items);
    }

    /**
     * Get all the keys from the collection.
     *
     * @return static
     */
    public function keys(): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_keys($this->items));
    }

    /**
     * Get all the values from the collection.
     *
     * @return static
     */
    public function values(): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_values($this->items));
    }
}