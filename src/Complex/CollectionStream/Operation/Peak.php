<?php

namespace Complex\CollectionStream\Operation;

class Peak extends AbstractOperation
{
    private $function;

    public function __construct($function)
    {
        $this->function = $function;
    }

    public function advance()
    {
        return $this->prev->advance();
    }

    public function pipe($obj)
    {
        call_user_func_array($this->function, array($obj));

        if ($this->next === null) {
            return $obj;
        }

        return $this->next->pipe($obj);
    }


}