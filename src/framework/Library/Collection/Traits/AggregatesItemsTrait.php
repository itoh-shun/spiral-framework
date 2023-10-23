<?php
namespace Collection\Traits;
trait AggregatesItemsTrait {
    /**
     * Get the sum of the items in the collection.
     * 
     * @return float
     */
    public function sum(): float {
        return array_sum($this->items);
    }

    /**
     * Get the average of the items in the collection.
     * 
     * @return float
     */
    public function avg(): float {
        if (empty($this->items)) {
            return 0.0;
        }
        return $this->sum() / count($this->items);
    }
}
