<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Exception\InvalidParameterException;
use Complex\CollectionStream\Exception\StreamConsumedException;
use Complex\CollectionStream\Operation\Filter;
use Complex\CollectionStream\Operation\Iterator;
use Complex\CollectionStream\Operation\Limit;
use Complex\CollectionStream\Operation\Map;
use Complex\CollectionStream\Operation\Operation;
use Complex\CollectionStream\Stream\ArrayStream;
use InvalidArgumentException;
use Iterator as StlIterator;

class OperationPipe
{
    /** @var Operation */
    private $lastOperation = null;
    private $isConsumed = false;

    public function __construct($iterable)
    {
        $collection = null;

        if ($iterable instanceof StlIterator) {
            $collection = $iterable;

        } elseif (is_array($iterable)) {
            $collection = new ArrayStream($iterable);

        } else {

            throw new InvalidParameterException(
                "Invalid constructor argument. Must be an array or implement the Iterator interface"
            );
        }

        $this->lastOperation = new Iterator($collection);
    }

    /**
     * @param Operation $operation
     */
    public function add(Operation $operation)
    {
        if ($this->lastOperation !== null) {
            $prevOperation = $this->lastOperation;
            $operation->setPrev($prevOperation);
            $prevOperation->setNext($operation);
        }

        $this->lastOperation = $operation;
    }

    private function next()
    {
        return $this->lastOperation->advance();
    }

    private function consumeStream()
    {
        if ($this->isConsumed) {
            throw new StreamConsumedException();
        }

        $this->isConsumed = true;
    }

    /*
     * Operations
     */

    public function limit($limit)
    {
        $this->add(new Limit($limit));

        return $this;
    }

    public function filter($function)
    {
        $this->add(new Filter($function));

        return $this;
    }

    public function map($function)
    {
        $this->add(new Map($function));

        return $this;
    }

    /*
     * Terminators
     */

    public function each($function)
    {
        $this->consumeStream();

        $current = null;

        while (($current = $this->next()) !== null) {
            call_user_func_array($function, array($current));
        }
    }

    public function toArray()
    {
        $streamValueArray = [];

        $this->each(function ($element) use (&$streamValueArray) {
            $streamValueArray[] = $element;
        });

        return $streamValueArray;
    }

    public function first()
    {
        $this->consumeStream();

        $result = $this->next();

        return Optional::ofNullable($result);
    }

    public function min($comparator = null)
    {
        $min = null;

        $this->each(function ($element) use (&$min, $comparator) {
            $value = $this->getComparableValue($element, $comparator);

            $this->compare($min, $value, function($min, $value) {
                return $value < $min;
            });
        });

        return $min;
    }

    public function max($comparator = null)
    {
        $max = null;

        $this->each(function ($element) use (&$max, $comparator) {
            $value = $this->getComparableValue($element, $comparator);

            $this->compare($max, $value, function($max, $value) {
                return $value > $max;
            });
        });

        return $max;
    }

    public function sum($comparator = null)
    {
        $sum = 0;

        $this->each(function ($element) use (&$sum, $comparator) {
            $value = $this->getComparableValue($element, $comparator);

            $sum += $value;
        });

        return $sum;
    }

    public function count()
    {
        $count = 0;

        $this->each(function ($element) use (&$count) {
            $count++;
        });

        return $count;
    }

    public function average($comparator = null)
    {
        $total = 0;
        $count = 0;

        $this->each(function ($element) use (&$total, &$count, $comparator) {
            $value = $this->getComparableValue($element, $comparator);

            $count++;
            $total += $value;
        });

        return $total / $count;
    }

    /*
     * Helpers
     */

    /**
     * Gets a comparable value through a closure or the element itself. Will throw an error for invalid types.
     *
     * @param mixed $element To be used as a parameter for the closure.
     * @param null $comparator Closure to fetch the comparable value.
     * @return mixed Comparable value
     */
    private function getComparableValue($element, $comparator = null) {
        $comparable = null;

        if ($comparator !== null) {
            $comparable = call_user_func($comparator, $element);
        } else {
            $comparable = $element;
        }

        if (!is_numeric($comparable)) {
            throw new InvalidArgumentException(sprintf('{0} is not numeric', gettype($comparable)));
        }

        return $comparable;
    }

    /**
     * Compares values using a closure with null as a lazy pre execution step.
     *
     * @param $closure
     * @param mixed $current The value to be compared against
     * @param mixed $comparable The value to be compared
     */
    private function compare(&$current, $comparable, $closure) {
        if ($current == null || call_user_func($closure, $current, $comparable)) {
            $current = $comparable;
        }
    }

} 