<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream;

class StreamTest extends \PHPUnit_Framework_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(1, 2, 3, 4, 5, 6);
    }

    public function testToArray() {
        $result = Stream::from($this->data)->toArray();

        $this->assertEquals($result, $this->data);
    }

}