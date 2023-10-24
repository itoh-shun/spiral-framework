<?php
namespace Collection\Traits;

use Collection\Collection;

trait OperatesOnItemsTrait {
    /**
     * Get all of the items in the collection.
     * 
     * @return array
     */
    public function all(): array {
        return $this->items;
    }

    /**
     * Apply a callback to each item and return a new collection.
     * 
     * @param callable $callback
     * @return Collection
     */
    public function map(callable $callback): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_map($callback, $this->items));
    }

    /**
     * Filter the items by a callback and return a new collection.
     * 
     * @param callable $callback
     * @return Collection
     */
    public function filter(callable $callback): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }
    

    /**
     * Filter the collection by a given key-value pair.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function where(string $key, $value): Collection {
        return $this->filter(function (Collection $item) use ($key, $value) {
            return $item->{$key} === $value;
        });
    }

    /**
     * Filter the collection by a given key-value pair where the value is not matching.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function whereNot(string $key, $value): Collection {
        return $this->filter(function (Collection $item) use ($key, $value) {
            return $item->{$key} !== $value;
        });
    }

    /**
     * Filter the collection by a given key-value pair where the value is in the given array.
     *
     * @param string $key
     * @param array $values
     * @return static
     */
    public function whereIn(string $key, array $values): Collection {
        return $this->filter(function (Collection $item) use ($key, $values) {
            return in_array($item->{$key}, $values);
        });
    }

    /**
     * Filter the collection by a given key-value pair where the value is not in the given array.
     *
     * @param string $key
     * @param array $values
     * @return static
     */
    public function whereNotIn(string $key, array $values): Collection {
        return $this->filter(function (Collection $item) use ($key, $values) {
            return !in_array($item->{$key}, $values);
        });
    }

    /**
     * Filter the collection using the given callback.
     *
     * @param callable $callback
     * @return static
     */
    public function reject(callable $callback): Collection {
        /** @phpstan-ignore-next-line */
        return new static(array_filter($this->items, function($item , $key) use ($callback) {
            return !$callback($item , $key);
        }, ARRAY_FILTER_USE_BOTH));
    }
}