<?php

require '../vendor/autoload.php';

use Complex\Demo\Person;
use Complex\CollectionStream\CollectionStream;

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
        $greaterThan26 = function (Person $p) {
            return ($p->getAge() > 26);
        };

        $agePersonBy10 = function (Person $p) {
            return new Person($p->getFirstName(), $p->getSurname(), $p->getAge() + 10, $p->getSex());
        };

        $println = function ($o) {
            echo $o . "\n";
        };

        $sortByAgeAsc = function (Person $a, Person $b) {
            return ($a->getAge() > $b->getAge());
        };

        $sortByAgeDesc = function (Person $a, Person $b) {
            return ($a->getAge() < $b->getAge());
        };

        $isMale = function(Person $p) {
            return $p->getSex() === Person::MALE;
        };

        $isFemale = function(Person $p) {
            return $p->getSex() === Person::FEMALE;
        };

        $sumAge = function ($c, Person $p) {
            $c += $p->getAge();
            return $c;
        };

        echo "List of people ordered by age:\n";
        echo "------------------------------\n";
        CollectionStream::from($this->exampleCollection)
            ->sort($sortByAgeAsc)
            ->each($println);

        echo "\nAge of men in 10 years above 26:\n";
        echo   "--------------------------------\n";
        CollectionStream::from($this->exampleCollection)
            ->map($agePersonBy10)
            ->filter($greaterThan26)
            ->filter($isMale)
            ->sort($sortByAgeAsc)
            ->reduce($sumAge)
            ->single($println);

        echo "\nTesting single process\n";
        echo   "----------------------\n";
        CollectionStream::from($this->exampleCollection)
            ->map($agePersonBy10)
            ->sort($sortByAgeDesc)
            ->single($println);
    }
}

$main = new Main();
$main->run();