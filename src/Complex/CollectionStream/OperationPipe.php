<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Operation\Filter;
use Complex\CollectionStream\Operation\Iterator;
use Complex\CollectionStream\Operation\Limit;
use Complex\CollectionStream\Operation\Map;
use Complex\CollectionStream\Operation\Operation;
use Complex\CollectionStream\Stream\ArrayStream;
use Exception;
use Iterator as StlIterator;

class OperationPipe
{
    /** @var Operation */
    private $lastOperation = null;

    public function __construct($iterable)
    {
        $collection = null;

        if ($iterable instanceof StlIterator) {
            $collection = $iterable;

        } elseif (is_array($iterable)) {
            $collection = new ArrayStream($iterable);

        } else {
            throw new Exception(
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
        $current = null;

        while (($current = $this->next()) !== null) {
            call_user_func_array($function, array($current));
        }
    }

    public function toArray()
    {
        $current = null;
        $streamValueArray = [];

        while (($current = $this->next()) !== null) {
            $streamValueArray[] = $current;
        }

        return $streamValueArray;
    }

    public function first()
    {
        $result = $this->next();

        return Optional::ofNullable($result);
    }
} 