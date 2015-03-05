<?php

require '../vendor/autoload.php';

use Complex\Demo\Person;
use Complex\CollectionStream\Stream;

class Main {
    private $exampleCollection;

    function __construct()
    {
        $this->exampleCollection = [
            new Person("Steven", "Inskip", 27, Person::MALE),
            new Person("Shaun", "Egan", 28, Person::MALE),
            new Person("Sheree", "Joubert", 24, Person::FEMALE),
            new Person("Cave", "Jew", 26, Person::MALE)
        ];
    }

    public function run() {
        $println = function ($s) {
            echo $s . "\n";
        };

        echo "Even numbers between 1 and 10:\n";
        Stream::range(1, 10)
            ->filter(function($i) {
                return ($i % 2 == 0);
            })
            ->map(function ($i) {
                return "Even " . $i;
            })
            ->each($println);

        echo "\nTest for inclusive range 1 to 3:\n";
        Stream::range(1, 3)
            ->each($println);

        echo "\nTest for inclusive range 5 to 1:\n";
        Stream::range(5, 1)
            ->each($println);

        echo "\nMale persons from example collection:\n";
        Stream::from($this->exampleCollection)
            ->filter(function(Person $person) {
                return ($person->getSex() == Person::MALE);
            })
            ->each($println);

        echo "\nLimit 3 of a range between 10 and 15:\n";
        Stream::range(10, 15)
            ->limit(3)
            ->each($println);

        echo "\nLazy test: \n";
        Stream::from(array("d2", "a2", "b1", "b3", "c"))
            ->filter(function($s) {
                echo "filter: " . $s . "\n";
                return (substr($s, 0, 1) === "a");
            })
            ->map(function($s) {
                echo "map: " . $s . "\n";
                return strtoupper($s);
            })
            ->each(function($s) {
                echo "each: " . $s . "\n";
            });
    }
}

$main = new Main();
$main->run();