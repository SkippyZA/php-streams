<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Exception\InvalidParameterException;
use Complex\CollectionStream\Exception\InvalidElementTypeException;
use Complex\CollectionStream\Exception\StreamConsumedException;
use Complex\CollectionStream\Operation\Filter;
use Complex\CollectionStream\Operation\Iterator;
use Complex\CollectionStream\Operation\Limit;
use Complex\CollectionStream\Operation\Map;
use Complex\CollectionStream\Operation\Operation;
use Complex\CollectionStream\Stream\ArrayStream;
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
        if($this->isConsumed)
            throw new StreamConsumedException();

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
        $this->consumeStream();

        $current = null;
        $streamValueArray = [];

        while (($current = $this->next()) !== null) {
            $streamValueArray[] = $current;
        }

        return $streamValueArray;
    }

    public function first()
    {
        $this->consumeStream();

        $result = $this->next();

        return Optional::ofNullable($result);
    }

    public function min()
    {
        $min = null;

        $this->each(function($element) use (&$min) {
            $this->enforceNumericElement($element);
            if ($min === null || $element < $min) $min = $element;
        });

        return $min;
    }

    public function max()
    {
        $max = null;

        $this->each(function($element) use (&$max) {
            $this->enforceNumericElement($element);
            if ($max === null || $element > $max) $max = $element;
        });

        return $max;
    }

    public function sum()
    {
        $sum = 0;

        $this->each(function ($element) use (&$sum) {
            $this->enforceNumericElement($element);
            $sum += $element;
        });

        return $sum;
    }

    private function enforceNumericElement($element)
    {
        if (!is_numeric($element)) throw new InvalidElementTypeException();
    }

} 