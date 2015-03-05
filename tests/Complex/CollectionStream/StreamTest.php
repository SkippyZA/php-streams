<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream;

class StreamTest extends \PHPUnit_Framework_TestCase {

    protected $data;
    protected $dataReversed;

    public function setUp() {
        $this->data = array(1, 2, 3, 4, 5, 6);
        $this->dataReversed = array(6, 5, 4, 3, 2, 1);
    }

    public function testToArray() {
        $result = Stream::from($this->data)->toArray();

        $this->assertEquals($result, $this->data);
    }

    public function testPositiveRange() {
        $result = Stream::range(1, 6)->toArray();

        $this->assertEquals($result, $this->data);
    }

    public function testNegativeRange() {
        $result = Stream::range(6, 1)->toArray();

        $this->assertEquals($result, $this->dataReversed);
    }

    public function testFilter() {
        $result = Stream::from($this->data)
            ->filter(function($i) { return $i % 2 == 0; })
            ->toArray();

        $this->assertEquals($result, array(2, 4, 6));
    }

    public function testMap() {
        $result = Stream::from($this->data)
            ->map(function($i) { return 1; })
            ->toArray();

        $this->assertEquals($result, array(1, 1, 1, 1, 1, 1));
    }

    public function testFilteredMap() {
        // bit more complex test -> get squares of odd numbers
        $result = Stream::from($this->data)
            ->filter(function($i) { return $i % 2 == 1; })
            ->map(function($i) { return $i * $i; })
            ->toArray();

        $this->assertEquals($result, array(1, 9, 25));
    }

    public function testLimit() {
        $result = Stream::from($this->data)
            ->limit(3)
            ->toArray();

        $this->assertEquals($result, array(1, 2, 3));
    }
}