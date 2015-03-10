<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream;

use Complex\Lib\Person;

class StreamTest extends \PHPUnit_Framework_TestCase {

    protected $data;
    protected $dataReversed;
    protected $people;

    public function setUp() {
        $this->data = array(1, 2, 3, 4, 5, 6);
        $this->dataReversed = array(6, 5, 4, 3, 2, 1);
        $this->people = [
            new Person("Steven", "Inskip", 27, Person::MALE),
            new Person("Shaun", "Egan", 28, Person::MALE),
            new Person("Sheree", "Joubert", 24, Person::FEMALE),
            new Person("Daniel", "Peters", 26, Person::MALE)
        ];
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

    public function testFirstHasValue() {
        $result = Stream::from($this->data)
            ->first();

        $this->assertTrue($result->isPresent());
        $this->assertEquals(1, $result->get());
    }

    public function testFirstWithoutValue() {
        $result = Stream::from($this->data)
            ->filter(function($i) {
                return false;
            })
            ->first();

        $this->assertFalse($result->isPresent());
    }

    public function testStreamCanOnlyBeConsumedOnce() {
        $this->setExpectedException('Complex\CollectionStream\Exception\StreamConsumedException');

        $stream = Stream::from($this->data)
            ->filter(function($i) {
                return true;
            });

        $result = $stream->first();
        $result2 = $stream->first(); // Exception should be thrown here
    }

    public function testMinTerminator() {
        $result = Stream::from($this->data)
            ->min()
            ->orElse(null);

        $this->assertEquals($result, 1);
    }

    public function testMinTerminatorWithComparator() {
        $result = Stream::from($this->people)
            ->min(array($this, 'getAge'))
            ->orElse(null);

        $this->assertEquals($result, 24);
    }

    public function testMinTerminatorWithEmptyStream()
    {
        $result = Stream::from(array())
            ->min()
            ->orElse(null);

        $this->assertEquals($result, null);
    }

    public function testMaxTerminator() {
        $result = Stream::from($this->data)
            ->max()
            ->orElse(null);

        $this->assertEquals($result, 6);
    }

    public function testMaxTerminatorWithComparator() {
        $result = Stream::from($this->people)
            ->max(array($this, 'getAge'))
            ->orElse(null);

        $this->assertEquals($result, 28);
    }

    public function testMaxTerminatorWithEmptyStream() {
        $result = Stream::from(array())
            ->max()
            ->orElse(null);

        $this->assertEquals($result, null);
    }

    public function testSumTerminator() {
        $result = Stream::from($this->data)
            ->sum();

        $this->assertEquals($result, 21);
    }

    public function testSumTerminatorWithComparator() {
        $result = Stream::from($this->people)
            ->sum(array($this, 'getAge'));

        $this->assertEquals($result, 105);
    }

    public function testCountTerminator() {
        $result = Stream::from($this->data)
            ->count();

        $this->assertEquals($result, 6);
    }

    public function testAverageTerminator()
    {
        $result = Stream::from(array(5, 6, 6, 7))
            ->average();

        $this->assertEquals($result, 6);
    }

    public function testAverageTerminatorWithComparator()
    {
        $result = Stream::from($this->people)
            ->average(array($this, 'getAge'));

        $this->assertEquals($result, 26.25);
    }

    public function getAge(Person $person) {
        return $person->getAge();
    }
}