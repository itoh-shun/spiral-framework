<?php
namespace Collection\Traits;

use JsonSerializable;
use Traversable;

trait ArrayableTrait {
    /**
     * Convert the collection's items to an array recursively.
     *
     * @return array
     */
    public function toArray(): array {
        return array_map(function ($value) {
            return $value instanceof self ? $value->toArray() : $value;
        }, $this->items);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int {
        return count($this->items);
    }
    /**
     * Convert various data types to array.
     * 
     * @param mixed $items
     * @return array
     */
    protected function getArrayableItems($items): array {
        if (is_array($items)) {
            return $items;
        }
        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }
        if ($items instanceof JsonSerializable) {
            return (array) $items->jsonSerialize();
        }
        if (is_object($items)) {
            return get_object_vars($items);
        }
        return [];
    }
}