<?php
namespace Collection;

use Collection\Traits\AggregatesItemsTrait;
use Collection\Traits\ArrayableTrait;
use Collection\Traits\ComparisonTrait;
use Collection\Traits\ManipulationTrait;
use Collection\Traits\OperatesOnItemsTrait;
use Collection\Traits\PaginationTrait;
use Collection\Traits\PropertyAccessTrait;
use Collection\Traits\SearchTrait;
use Collection\Traits\TransformationTrait;

class Collection {
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
}