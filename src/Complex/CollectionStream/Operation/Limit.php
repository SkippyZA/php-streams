<?php

namespace Complex\CollectionStream\Operation;

class Limit extends AbstractOperation {
    private $count = 0;
    private $limit;

    public function __construct($limit) {
        $this->limit = $limit;
    }

    public function advance()
    {
        return $this->prev->advance();
    }

    public function pipe($obj)
    {
        if($this->count >= $this->limit) {
            return null;
        }

        $this->count++;

        if($this->next === null) {
            return $obj;
        }

        return $this->next->pipe($obj);
    }
}