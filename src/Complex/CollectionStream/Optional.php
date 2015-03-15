<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Exception\NoSuchElementException;
use \Exception;
use \InvalidArgumentException;

class Optional {
    private $var;

    private function __construct($var) {
        $this->var = $var;
    }

    /**
     * Create a new Optional of the passed variable
     *
     * @param $var
     * @return Optional
     */
    public static function of($var) {
        return new Optional($var);
    }

    /**
     * Create an empty Optional
     *
     * @return Optional
     */
    public static function ofEmpty() {
        return new Optional(null);
    }

    /**
     * Create an Optional of the variable if present, otherwise create an empty Optional.
     *
     * @param $var
     * @return Optional
     */
    public static function ofNullable($var) {
        if(self::_isNull($var)) {
            return new Optional(null);
        } else {
            return new Optional($var);
        }
    }

    /**
     * Check if Optional contains a non-null value
     *
     * @return bool
     */
    public function isPresent() {
        return !self::_isNull($this->var);
    }

    /**
     * If present, invoke the passed function, otherwise do nothing
     *
     * @param $func
     */
    public function ifPresent($func) {
        if(!$this->isPresent())
            return;

        call_user_func_array($func, array($this->var));
    }

    /**
     * If the value is present, return it, otherwise throws NoSuchElementException
     *
     * @return mixed
     * @throws NoSuchElementException
     */
    public function get() {
        if(self::_isNull($this->var))
            throw new NoSuchElementException();

        return $this->var;
    }

    /**
     * Return the value if present, otherwise return the passed value
     *
     * @param mixed $var Value to return if none present
     * @return mixed
     */
    public function orElse($var) {
        return (self::_isNull($this->var) ? $var : $this->var);
    }

    /**
     * Return the value if present, otherwise return the result of the passed function
     *
     * @param $func
     * @return mixed
     */
    public function orElseGet($func) {
        if(!self::_isNull($this->var))
            return $this->var;

        return call_user_func($func);
    }

    /**
     * Return the value if present, otherwise throw the user specified exception
     *
     * @param $exception
     * @return mixed
     * @throws InvalidArgumentException Thrown if parameter is not an instance of Exception
     * @throws Exception
     */
    public function orElseThrow($exception) {
        if(!$exception instanceof Exception) {
            throw new InvalidArgumentException("Argument is not instance of Exception");
        }

        if(!self::_isNull($this->var)) {
            return $this->var;
        } else {
            throw $exception;
        }
    }

    /**
     * Check for a null variable
     *
     * @param $var
     * @return bool
     */
    private static function _isNull($var) {
        return ($var === null);
    }
}