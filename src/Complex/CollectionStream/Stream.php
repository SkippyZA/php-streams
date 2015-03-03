<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Stream\RangeStream;
use Exception;

class Stream
{
    /**
     * @param $collection
     *
     * @throws Exception
     */
    private function __construct($collection)
    {

    }

    /**
     * @param $collection
     *
     * @return OperationPipe
     */
    public static function from($collection)
    {
        return new OperationPipe($collection);
    }

    /**
     * @param int $start Start inclusive
     * @param int $end   End inclusive
     *
     * @return OperationPipe
     */
    public static function range($start, $end)
    {
        return new OperationPipe(new RangeStream($start, $end));
    }


}