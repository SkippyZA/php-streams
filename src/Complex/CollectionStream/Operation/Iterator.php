<?php

namespace Complex\CollectionStream\Operation;

use Complex\CollectionStream\Exception\NotImplementedException;
use Iterator as StlIterator;

class Iterator extends AbstractOperation
{
    /** @var \Iterator */
    private $iterator;

    public function __construct(StlIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function advance()
    {
        $this->iterator->next();

        $obj = $this->iterator->valid() ? $this->iterator->current() : null;

        if ($obj === null) {
            return $obj;
        }

        if ($this->next === null) {
            return $obj;
        }

        return $this->next->pipe($obj);
    }

    public function pipe($obj)
    {
        throw new NotImplementedException();
    }
}