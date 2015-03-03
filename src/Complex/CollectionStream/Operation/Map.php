<?php

namespace Complex\CollectionStream\Operation;

class Map extends AbstractOperation
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
        $result = call_user_func_array($this->function, array($obj));

        if ($this->next === null) {
            return $result;
        }

        return $this->next->pipe($result);
    }


}