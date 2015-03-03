<?php

namespace Complex\CollectionStream;

use Complex\CollectionStream\Stream\ArrayStream;
use Complex\CollectionStream\Stream\RangeStream;
use Iterator;
use Exception;

class Stream {
    /**
     * @var Iterator
     */
    private $collection;


    /**
     * @param $collection
     * @throws Exception
     */
    function __construct($collection) {
        if($collection instanceof Iterator) {
            $this->collection = $collection;

        } elseif(is_array($collection)) {
            $this->collection = new ArrayStream($collection);
        } else {
            throw new Exception("Invalid constructor argument. Must be an array or implement the Iterator interface");
        }
    }

    /**
     * @param $collection
     * @return Stream
     */
    public static function from($collection) {
        return new Stream($collection);
    }

    /**
     * @param int $start Start inclusive
     * @param int $end End inclusive
     * @return Stream
     */
    public static function range($start, $end) {
        return new Stream(new RangeStream($start, $end));
    }

    public function test() {
        foreach($this->collection as $value) {
            echo $value . "\n";
        }
    }
}