<?php

namespace Complex\CollectionStream\Operation;


class Stateful extends AbstractOperation
{
    private $filterCallable;
    private $buffer;
    private $i = 0;

    public function __construct(callable $filterCallable)
    {
        $this->filterCallable = $filterCallable;
    }

    public function advance()
    {
        $obj = null;

        if($this->buffer === null) {
            $this->buffer = [];

            while (($obj = $this->prev->advance()) !== null) {
                $this->i++;
            }
        }

        if(count($this->buffer) === 0) {
            return null;
        }

        $obj = array_shift($this->buffer);

        if($this->next === null) {
            return $obj;
        }

        return $this->next->pipe($obj);
    }

    public function pipe($obj)
    {
        $functionResult = null;

        if($this->filterCallable !== null) {
            $functionResult = (bool) call_user_func_array($this->filterCallable, [$obj, $this->i, $this->buffer]);
        }

        if($functionResult) {
            array_push($this->buffer, $obj);
        }

        return $obj;
    }
}