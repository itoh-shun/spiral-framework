<?php
namespace Collection;

use ArrayIterator;
use Collection\Traits\AggregatesItemsTrait;
use Collection\Traits\ArrayableTrait;
use Collection\Traits\ComparisonTrait;
use Collection\Traits\ManipulationTrait;
use Collection\Traits\OperatesOnItemsTrait;
use Collection\Traits\PaginationTrait;
use Collection\Traits\PropertyAccessTrait;
use Collection\Traits\SearchTrait;
use Collection\Traits\TransformationTrait;
use Countable;
use IteratorAggregate;

class Collection implements IteratorAggregate , Countable {
    use ArrayableTrait, OperatesOnItemsTrait, AggregatesItemsTrait;
    use AggregatesItemsTrait, ComparisonTrait, ManipulationTrait;
    use OperatesOnItemsTrait, PaginationTrait, SearchTrait;
    use TransformationTrait, PropertyAccessTrait;

    /**
     * The items contained in the collection.
     * 
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     * 
     * @param mixed $items
     */
    public function __construct($items = []) {
        $this->items = $this->getArrayableItems($items);

        // Convert sub-arrays to Collection instances
        foreach ($this->items as $key => $value) {
            if (is_array($value)) {
                $this->items[$key] = new self($value);
            }
        }
    }

    /**
     * Retrieve an external iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->items);
    }
    
    
    /**
     * Count elements of the collection.
     *
     * @return int
     */
    public function count(): int {
        return count($this->items);
    }
}