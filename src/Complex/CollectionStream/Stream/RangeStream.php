<?php

namespace Complex\CollectionStream\Stream;

use Iterator;

class RangeStream implements Iterator
{
    private $begin;
    private $end;
    private $current;

    function __construct($begin, $end)
    {
        $this->begin = $begin;
        $this->end = $end;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->current === null) {
            $this->current = $this->begin;
        } else if ($this->isPositiveRange()) {
            $this->current++;
        } else {
            $this->current--;
        }
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if ($this->isValid()) {
            return $this->current();
        }

        return null;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        if ($this->isPositiveRange()) {
            // positive direction range validity
            if ($this->current >= $this->begin && $this->current <= $this->end) {
                return true;
            }
        } else {
            // negative direction range validity
            if ($this->current <= $this->begin && $this->current >= $this->end) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->current = $this->begin;
    }

    /**
     * Tests the range parameters for positive direction increments
     *
     * @return boolean Will be true for positive range, false for negative
     */
    private function isPositiveRange()
    {
        return $this->begin <= $this->end;
    }

} 