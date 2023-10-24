<?php
namespace Collection\Traits;

use Collection\Collection;
trait PaginationTrait {
    /**
     * Paginate the collection.
     *
     * @param  int  $perPage
     * @param  int  $page
     * @return static
     */
    public function paginate(int $perPage = 15, int $page = 1): Collection {
        $start = ($page - 1) * $perPage;
        $slicedItems = array_slice($this->items, $start, $perPage);
        /** @phpstan-ignore-next-line */
        return new static($slicedItems);
    }

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param  int  $size
     * @return array
     */
    public function chunk(int $size): array {
        $chunks = [];
        $count = count($this->items);
        
        for ($i = 0; $i < $count; $i += $size) {
            /** @phpstan-ignore-next-line */
            $chunks[] = new static(array_slice($this->items, $i, $size));
        }
        
        return $chunks;
    }
}