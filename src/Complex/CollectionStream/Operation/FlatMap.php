<?php

namespace Complex\CollectionStream\Operation;

use Complex\CollectionStream\Stream\ArrayStream;
use \Iterator as StlIterator;

class FlatMap extends AbstractOperation
{
    /** @var StlIterator */
    private $iterator;

    public function advance()
    {
        if ($this->iterator === null) {
            return $this->prev->advance();
        }

        $this->iterator->next();

        $obj = $this->iterator->valid() ? $this->iterator->current() : null;

        if ($obj === null) {
            $this->iterator = null;
            return $this->prev->advance();
        }

        if ($this->next === null) {
            return $obj;
        }

        return $this->next->pipe($obj);
    }

    public function pipe($obj)
    {
        if ($obj instanceof StlIterator) {
            $this->iterator = $obj;

        } elseif (is_array($obj)) {
            $this->iterator = new ArrayStream($obj);

        } else {
            throw new \InvalidArgumentException(
                "Invalid obj for flatMap. Must be array or implement the Iterator interface"
            );
        }

        $this->iterator->next();

        $current = $this->iterator->valid() ? $this->iterator->current() : null;

        if ($current === null) {
            return $this->prev->advance();
        }

        if ($this->next === null) {
            return $current;
        }

        return $this->next->pipe($current);
    }
}