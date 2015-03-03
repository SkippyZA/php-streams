<?php
namespace Complex\CollectionStream\Operation;

interface Operation
{
    /**
     * @return mixed|null
     */
    public function advance();

    /**
     * @param $obj
     *
     * @return mixed
     */
    public function pipe($obj);

    /**
     * @param Operation $prev
     *
     * @return Operation
     */
    public function setPrev(Operation $prev);

    /**
     * @param Operation $next
     *
     * @return Operation
     */
    public function setNext(Operation $next);
} 