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
        echo "::range(1, 10)\n";
        Stream::range(1, 10)->test();

        echo "\n::from(\$this->exampleCollection)\n";
        Stream::from($this->exampleCollection)->test();

        echo "\n::from(new RangeStream(1, 10))\n";
        Stream::from(new \Complex\CollectionStream\Stream\RangeStream(1, 10))->test();

    }
}

$main = new Main();
$main->run();