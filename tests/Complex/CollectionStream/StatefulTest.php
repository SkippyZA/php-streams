<?php

namespace Complex\CollectionStream;

class StatefulTest extends \PHPUnit_Framework_TestCase {
    public function testGeneralUsecase()
    {
        $source = [1, 2, 1, 1, 2, 3, 2]; // 1 = 3x, 2 = 3x, 3 = 1x
        $expected = [1, 2, 3];

        $result = Stream::from($source)
            ->distinct()
            ->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testDistinctObjects()
    {
        $obj1 = new \stdClass();
        $obj1->value = 1;
        $obj2 = new \stdClass();
        $obj2->value = 2;
        $obj3 = new \stdClass();
        $obj3->value = 3;
        $obj4 = new \stdClass();
        $obj4->value = 2;

        $source = [
            $obj1, $obj2, $obj3, $obj4
        ];

        $expected = [
            $obj1, $obj4, $obj3
        ];

        $result = Stream::from($source)
            ->distinct()
            ->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testStrictDistinctObjects()
    {
        $obj1 = new \stdClass();
        $obj1->value = 1;
        $obj2 = new \stdClass();
        $obj2->value = 2;
        $obj3 = new \stdClass();
        $obj3->value = 3;
        $obj4 = new \stdClass();
        $obj4->value = 2;

        $source = [
            $obj1, $obj2, $obj3, $obj4
        ];

        $expected = [$obj1, $obj2, $obj3, $obj4];

        $result = Stream::from($source)
            ->distinct(true)
            ->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testDistinctWithOperatorsAfterwards()
    {
        $source = [1, 2, 1, 1, 2, 3, 2];
        $expected = [8, 12];

        $result = Stream::from($source)
            ->distinct()
            ->map(function($i) { return $i * 4; })
            ->filter(function($i) { return $i > 5; })
            ->toArray();

        $this->assertEquals($expected, $result);
    }
}
 