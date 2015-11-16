<?php

namespace Complex\CollectionStream;

class StatefulTest extends \PHPUnit_Framework_TestCase {
    public function testGeneralUsecase() {
        $source = [1, 2, 1, 1, 2, 3, 2]; // 1 = 3x, 2 = 3x, 3 = 1x
        $expected = [1, 2, 3];

        $result = Stream::from($source)
            ->distinct()
            ->toArray();

        $this->assertEquals($expected, $result);
    }
}
 