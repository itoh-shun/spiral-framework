<?php

namespace Collection\Traits;

use Collection\Collection;

trait ComparisonTrait {

    /**
     * Get the items that are not present in the given items.
     *
     * @param  array|Collection  $items
     * @return static
     */
    public function diff($items): Collection {
        if ($items instanceof Collection) {
            $items = $items->all();
        }
        /** @phpstan-ignore-next-line */
        return new static(array_diff($this->items, $items));
    }

    /**
     * Get the items that are also present in the given items.
     *
     * @param  array|Collection  $items
     * @return static
     */
    public function intersect($items): Collection {
        if ($items instanceof Collection) {
            $items = $items->all();
        }
        /** @phpstan-ignore-next-line */
        return new static(array_intersect($this->items, $items));
    }

    /**
     * Get the unique items in the collection.
     *
     * @return static
     */
    public function unique(): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_unique($this->items));
    }
}