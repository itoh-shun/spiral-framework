<?php

use Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testBasicAccess()
    {
        $collection = new Collection(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->assertEquals(1, $collection->get('a'));
        $this->assertEquals(2, $collection->get('b'));
        $this->assertEquals(3, $collection->get('c'));
    }

    public function testAllToArrayAndCount()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $collection->all());
        $this->assertEquals(['a', 'b', 'c'], $collection->toArray());
        $this->assertEquals(3, $collection->count());
    }

    public function testSumAvgMaxMin()
    {
        $collection = new Collection([10, 20, 30]);
        $this->assertEquals(60, $collection->sum());
        $this->assertEquals(20, $collection->avg());
        $this->assertEquals(30, $collection->max());
        $this->assertEquals(10, $collection->min());
    }

    public function testWhereMethods()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Charlie'],
        ]);

        $this->assertCount(1, $collection->where('name', 'Alice'));
        $this->assertCount(2, $collection->whereNot('name', 'Alice'));
        $this->assertCount(2, $collection->whereIn('id', [1, 2]));
        $this->assertCount(1, $collection->whereNotIn('id', [1, 2]));
    }

    public function testFilterRejectFirstLastGetColumn()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e']);
        $collection->reject(function ($value) {
            return $value === 'a' || $value === 'e';
        });
        $this->assertCount(2, $collection->filter(function ($value) {
            return $value === 'a' || $value === 'e';
        }));
        $this->assertCount(3, $collection->reject(function ($value) {
            var_dump($value);
            return $value === 'a' || $value === 'e';
        }));
        $this->assertEquals('a', $collection->first());
        $this->assertEquals('e', $collection->last());
        $this->assertEquals('c', $collection->get(2));

        $collectionOfObjects = new Collection([
            (object) ['id' => 1, 'name' => 'Alice'],
            (object) ['id' => 2, 'name' => 'Bob'],
            (object) ['id' => 3, 'name' => 'Charlie'],
        ]);
        $this->assertEquals(['Alice', 'Bob', 'Charlie'], $collectionOfObjects->column('name'));
    }
}