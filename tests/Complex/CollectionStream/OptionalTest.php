<?php

namespace Complex\CollectionStream;

use \Exception;
use \stdClass;

class OptionalTest extends \PHPUnit_Framework_TestCase {
    public function testOptionalOf() {
        $expected = "Hello world";

        $opt = Optional::of($expected);

        $this->assertTrue($opt->isPresent());
        $this->assertEquals($expected, $opt->get());
    }

    public function testOptionalOfEmpty() {
        $emptyOptional = Optional::ofEmpty();

        $this->assertFalse($emptyOptional->isPresent());
    }

    public function testEmptyOptionalThrowsExceptionOnGet() {
        $this->setExpectedException('Complex\CollectionStream\Exception\NoSuchElementException');

        $emptyOptional = Optional::ofEmpty();
        $emptyOptional->get(); // Expected to throw exception
    }

    public function testOptionalOfNullableWithValue() {
        $expected = "Hello world";

        $opt = Optional::ofNullable($expected);

        $this->assertTrue($opt->isPresent());
        $this->assertEquals($expected, $opt->get());
    }

    public function testOptionalOfNullableWithoutValue() {
        $opt = Optional::ofNullable(null);

        $this->assertFalse($opt->isPresent());
    }

    public function testOptionalIfPresent() {
        $hasRun = false;
        $expected = "Hello world";

        $opt = Optional::of($expected);
        $opt->ifPresent(function($v) use ($expected, &$hasRun) {
            $this->assertEquals($v, $expected);
            $hasRun = true;
        });

        $this->assertTrue($hasRun);
    }

    public function testEmptyOptionalUsingOrElse() {
        $expected = "Hello world";

        $opt = Optional::ofEmpty();
        $result = $opt->orElse($expected);

        $this->assertFalse($opt->isPresent());
        $this->assertEquals($expected, $result);
    }

    public function testEmptyOptionalUsingOrElseGet() {
        $expected = "From function";

        $opt = Optional::ofEmpty();
        $result = $opt->orElseGet(function() {
            return "From function";
        });

        $this->assertFalse($opt->isPresent());
        $this->assertEquals($expected, $result);
    }

    public function testOrElseThrowWithValidValue() {
        $expected = "Hello world";

        $opt = Optional::of($expected);
        $result = $opt->orElseThrow(new Exception());

        $this->assertTrue($opt->isPresent());
        $this->assertEquals($expected, $result);
    }

    public function testEmptyOptionalUsingOrElseThrow() {
        $this->setExpectedException("Exception");

        $opt = Optional::ofEmpty();
        $this->assertFalse($opt->isPresent());
        $opt->orElseThrow(new Exception());
    }

    public function testOrElseThrowWithInvalidException() {
        $this->setExpectedException('InvalidArgumentException');

        $opt = Optional::ofEmpty();
        $this->assertFalse($opt->isPresent());
        $opt->orElseThrow(new stdClass());
    }
}
 