<?php

namespace Complex\CollectionStream;

use Complex\Lib\Person;
use Complex\CollectionStream;

class StreamTest extends \PHPUnit_Framework_TestCase
{

    protected $data;
    protected $dataReversed;
    protected $people;

    public function setUp()
    {
        $this->data = array(1, 2, 3, 4, 5, 6);
        $this->dataReversed = array(6, 5, 4, 3, 2, 1);
        $this->people = [
            new Person("Steven", "Inskip", 27, Person::MALE),
            new Person("Shaun", "Egan", 28, Person::MALE),
            new Person("Sheree", "Joubert", 24, Person::FEMALE),
            new Person("Daniel", "Peters", 26, Person::MALE)
        ];
    }

    public function testToArray()
    {
        $result = Stream::from($this->data)->toArray();

        $this->assertEquals($result, $this->data);
    }

    public function testPositiveRange()
    {
        $result = Stream::range(1, 6)->toArray();

        $this->assertEquals($result, $this->data);
    }

    public function testNegativeRange()
    {
        $result = Stream::range(6, 1)->toArray();

        $this->assertEquals($result, $this->dataReversed);
    }

    public function testFilter()
    {
        $result = Stream::from($this->data)
            ->filter(function ($i) {
                    return $i % 2 == 0;
                })
            ->toArray();

        $this->assertEquals($result, array(2, 4, 6));
    }

    public function testMap()
    {
        $result = Stream::from($this->data)
            ->map(function ($i) {
                    return 1;
                })
            ->toArray();

        $this->assertEquals($result, array(1, 1, 1, 1, 1, 1));
    }

    public function testFilteredMap()
    {
        // bit more complex test -> get squares of odd numbers
        $result = Stream::from($this->data)
            ->filter(function ($i) {
                    return $i % 2 == 1;
                })
            ->map(function ($i) {
                    return $i * $i;
                })
            ->toArray();

        $this->assertEquals($result, array(1, 9, 25));
    }

    public function testLimit()
    {
        $result = Stream::from($this->data)
            ->limit(3)
            ->toArray();

        $this->assertEquals($result, array(1, 2, 3));
    }

    public function testFirstHasValue()
    {
        $result = Stream::from($this->data)
            ->first();

        $this->assertTrue($result->isPresent());
        $this->assertEquals(1, $result->get());
    }

    public function testFirstWithoutValue()
    {
        $result = Stream::from($this->data)
            ->filter(function ($i) {
                return false;
            })
            ->first();

        $this->assertFalse($result->isPresent());
    }

    public function testStreamCanOnlyBeConsumedOnce()
    {
        $this->setExpectedException('Complex\CollectionStream\Exception\StreamConsumedException');

        $stream = Stream::from($this->data)
            ->filter(function ($i) {
                return true;
            });

        $result = $stream->first();
        $result2 = $stream->first(); // Exception should be thrown here
    }

    public function testMinTerminator()
    {
        $result = Stream::from($this->data)
            ->min()
            ->orElse(null);

        $this->assertEquals($result, 1);
    }

    public function testMinTerminatorWithComparator()
    {
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

    public function testMaxTerminator()
    {
        $result = Stream::from($this->data)
            ->max()
            ->orElse(null);

        $this->assertEquals($result, 6);
    }

    public function testMaxTerminatorWithComparator()
    {
        $result = Stream::from($this->people)
            ->max(array($this, 'getAge'))
            ->orElse(null);

        $this->assertEquals($result, 28);
    }

    public function testMaxTerminatorWithEmptyStream()
    {
        $result = Stream::from(array())
            ->max()
            ->orElse(null);

        $this->assertEquals($result, null);
    }

    public function testSumTerminator()
    {
        $result = Stream::from($this->data)
            ->sum()
            ->orElse(null);

        $this->assertEquals($result, 21);
    }

    public function testSumTerminatorWithComparator()
    {
        $result = Stream::from($this->people)
            ->sum(array($this, 'getAge'))
            ->orElse(null);

        $this->assertEquals($result, 105);
    }

    public function testSumTerminatorWithEmptyStream()
    {
        $result = Stream::from(array())
            ->sum()
            ->orElse(null);

        $this->assertEquals($result, null);
    }

    public function testCountTerminator()
    {
        $result = Stream::from($this->data)
            ->count();

        $this->assertEquals($result, 6);
    }

    public function testCountTerminatorWithEmptyStream()
    {
        $result = Stream::from(array())
            ->count();

        $this->assertEquals($result, 0);
    }

    public function testAverageTerminator()
    {
        $result = Stream::from(array(5, 6, 6, 7))
            ->average()
            ->orElse(null);

        $this->assertEquals($result, 6);
    }

    public function testAverageTerminatorWithComparator()
    {
        $result = Stream::from($this->people)
            ->average(array($this, 'getAge'))
            ->orElse(null);

        $this->assertEquals($result, 26.25);
    }

    public function testAverageTerminatorWithEmptyStream()
    {
        $result = Stream::from(array())
            ->average()
            ->orElse(null);

        $this->assertEquals($result, null);
    }

    public function testAverageTerminatorWithZeroAverage()
    {
        $result = Stream::from(array(0, 0, 0, 0))
            ->average()
            ->orElse(null);

        $this->assertEquals($result, 0);
    }

    public function testReduceTerminator()
    {
        // performing a sum reduction on people's age
        $result = Stream::from($this->people)
            ->reduce(0, function ($result, Person $person) {
                return $result + $person->getAge();
            })
            ->orElse(null);

        $this->assertEquals($result, 105);
    }

    public function testFlatMap()
    {
        $frontend = new \stdClass();
        $frontend->name = "Frontend";
        $frontend->staff = array(
            new Person("John", "Marley", 38, Person::MALE),
            new Person("Some", "Woman", 28, Person::FEMALE)
        );

        $backend = new \stdClass();
        $backend->name = "Backend";
        $backend->staff = array(
            new Person("Steven", "Inskip", 27, Person::MALE),
            new Person("Shaun", "Egan", 52, Person::MALE),
            new Person("Eager", "Beaver", 18, Person::FEMALE)
        );

        $creative = new \stdClass();
        $creative->name = "Creative";
        $creative->staff = array(
            new Person("Bob", "World", 42, Person::MALE),
            new Person("Sally", "Sue", 24, Person::FEMALE),
            new Person("Hippy", "McGee", 21, Person::FEMALE),
            new Person("Bare", "Foot", 26, Person::MALE),
        );

        $teams = array($frontend, $backend, $creative);

        $expected = array(
            "Sally",
            "Hippy"
        );

        $result = Stream::from($teams)
            ->filter(function ($obj) {                       // Only the creative team
                return ($obj->name === "Creative");
            })
            ->flatMap(function ($obj) {                      // Get the staff members
                return $obj->staff;
            })
            ->filter(function (Person $p) {                  // Only females
                return ($p->getSex() == Person::FEMALE);
            })
            ->map(function (Person $p) {                     // Get their first names
                return $p->getFirstName();
            })
            ->toArray();                                    // Return an array of creative teams female first names

        $this->assertEquals($expected, $result);

        // Max age of all team members
        $maxAge = Stream::from($teams)
            ->flatMap(function ($obj) {
                return $obj->staff;
            })
            ->max(function (Person $p) {
                return $p->getAge();
            })
            ->get();

        $this->assertEquals(52, $maxAge);
    }

    public function testPeak()
    {
        $count = 1;

        Stream::from($this->data)
            ->peak(function($i) use (&$count) {
                $this->assertEquals($count, $i);

                return -1; // This should not effect the stream
            })
            ->each(function($i) use (&$count) {
                $this->assertEquals($count, $i);
                $count++;
            });
    }

    public function testIterator()
    {
        $expectedArray = [2, 4, 6];
        $iteratorCount = 0;

        $evenStream = Stream::from($this->data)
            ->filter(function($d) {
                return $d % 2 === 0;
            });

        foreach($evenStream->iterate() as $evenNumber) {
            $this->assertTrue($expectedArray[$iteratorCount++] === $evenNumber);
        }

        $this->assertEquals(3, $iteratorCount);
    }


    public function getAge(Person $person)
    {
        return $person->getAge();
    }
}