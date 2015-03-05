<?php
namespace Complex\CollectionStream\Tests;

use Complex\CollectionStream\Stream;


class StreamTest extends \PHPUnit_Framework_TestCase {

    public function testToArray() {
        $expectedArray = array(1,2,3);
        $result = Stream::from(array(1,2,3))->toArray();

        $this->assertTrue($expectedArray === $result);
    }

}