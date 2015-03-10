<?php

namespace Complex\Lib; // TODO needs to be refactored


class Person
{
    const MALE = "male";
    const FEMALE = "female";

    private $firstName = "";
    private $surname = "";
    private $age = 0;
    private $sex = Person::MALE;

    /**
     * @param string $firstName
     * @param string $surname
     * @param int    $age
     * @param string $sex
     */
    function __construct($firstName, $surname, $age, $sex)
    {
        $this->age = $age;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->sex = $sex;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->getFirstName() . " " . $this->getSurname();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    function __toString()
    {
        return $this->getFirstName() . " " . $this->getSurname() . ", Age: " . $this->getAge() . ", Sex: "
        . $this->getSex();
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }


} 