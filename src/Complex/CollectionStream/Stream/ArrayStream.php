<?php

namespace Complex\CollectionStream\Stream;


class ArrayStream implements \Iterator
{
    private $collection;
    private $index = 0;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * Return the current element
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->collection[$this->index];
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->index;
    }


    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if (isset($this->collection[$this->index]))
            return $this->index;

        return null;
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->collection[$this->index]);

    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->index = 0;
    }
} 