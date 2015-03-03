<?php

namespace Complex\CollectionStream\Operation;

abstract class AbstractOperation implements Operation
{
    /** @var  Operation */
    protected $prev;
    /** @var  Operation */
    protected $next;

    /**
     * @inheritdoc
     */
    abstract public function pipe($obj);

    /**
     * @inheritdoc
     */
    abstract public function advance();

    /**
     * @inheritdoc
     */
    public function setPrev(Operation $prev)
    {
        $this->prev = $prev;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setNext(Operation $next)
    {
        $this->next = $next;

        return $this;
    }
}