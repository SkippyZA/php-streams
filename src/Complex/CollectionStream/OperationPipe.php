<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Exception\StreamConsumedException;
use Complex\CollectionStream\Operation\Filter;
use Complex\CollectionStream\Operation\FlatMap;
use Complex\CollectionStream\Operation\Iterator;
use Complex\CollectionStream\Operation\Limit;
use Complex\CollectionStream\Operation\Map;
use Complex\CollectionStream\Operation\Peak;
use Complex\CollectionStream\Operation\Operation;
use Complex\CollectionStream\Operation\Stateful;
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

            throw new InvalidArgumentException(
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

    public function filter(callable $function)
    {
        $this->add(new Filter($function));

        return $this;
    }

    public function map(callable $function)
    {
        $this->add(new Map($function));

        return $this;
    }

    public function flatMap(callable $function)
    {
        $this->add(new Map($function));

        $this->add(new FlatMap());

        return $this;
    }

    public function peak(callable $function)
    {
        $this->add(new Peak($function));

        return $this;
    }

    public function distinct()
    {
        $this->add(new Stateful(function($obj, $i, $array) {
            return !in_array($obj, $array);
        }));

        return $this;
    }

    /*
     * Terminators
     */

    public function each(callable $function)
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

    public function reduce($identity, callable $accumulator)
    {
        $result = $identity;

        $this->each(function ($element) use (&$result, $accumulator) {
            $result = call_user_func($accumulator, $result, $element);
        });

        return Optional::ofNullable($result);
    }

    public function min(callable $comparator = null)
    {
        return $this->reduce(null, function ($min, $element) use ($comparator) {
            $value = $this->getComparableValue($element, $comparator);

            return $this->compare($min, $value, function ($min, $value) {
                return $value < $min;
            });
        });
    }

    public function max(callable $comparator = null)
    {
        return $this->reduce(null, function ($max, $element) use ($comparator) {
            $value = $this->getComparableValue($element, $comparator);

            return $this->compare($max, $value, function ($max, $value) {
                return $value > $max;
            });
        });
    }

    public function sum(callable $comparator = null)
    {
        return $this->reduce(null, function ($sum, $element) use ($comparator) {
            $value = $this->getComparableValue($element, $comparator);

            return $sum + $value;
        });
    }

    public function count()
    {
        return $this->reduce(0, function ($count, $i) {
            return ++$count;
        })->get();
    }

    public function average(callable $comparator = null)
    {
        $averageData = $this->reduce(array('total' => 0, 'count' => 0),
            function ($metrics, $element) use ($comparator) {
                $metrics['total'] += $this->getComparableValue($element, $comparator);
                $metrics['count'] += 1;
                return $metrics;
            })->get();

        if ($averageData['count'] == 0) {
            return Optional::ofEmpty();
        }

        return Optional::ofNullable($averageData['total'] / $averageData['count']);
    }

    /**
     *
     * @return \Generator
     */
    public function iterate()
    {
        $current = null;

        while (($current = $this->next()) !== null) {
            yield $current;
        }
    }

    /*
     * Helpers
     */

    /**
     * Gets a comparable value through a closure or the element itself. Will throw an error for invalid types.
     *
     * @param mixed $element To be used as a parameter for the closure.
     * @param callable $comparator Closure to fetch the comparable value.
     * @return mixed Comparable value
     */
    private function getComparableValue($element, callable $comparator = null)
    {
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
     * Compares values using a closure. If the current value is null, the comparison will not be executed and $current
     * will take on the value as if the result of the comparison were True.
     *
     * @param mixed $current The value to be compared against
     * @param mixed $comparable The value to be compared
     * @param $closure
     * @return mixed
     */
    private function compare($current, $comparable, callable $closure)
    {
        if ($current == null || call_user_func($closure, $current, $comparable)) {
            return $comparable;
        }

        return $current;
    }

} 