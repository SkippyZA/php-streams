<?php

namespace Complex\CollectionStream;


class CollectionStream {
    private $collection;

    /**
     * @param $collection
     */
    function __construct($collection) {
        $this->collection = $collection;
    }

    /**
     * @param $collection
     * @return CollectionStream
     */
    public static function from($collection) {
        return new CollectionStream($collection);
    }

    /**
     * @return $this
     */
    public function map($callback) {
        $this->collection = array_map($callback, $this->collection);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function filter($callback) {
        $this->collection = array_filter($this->collection, $callback);
        return $this;
    }

    /**
     * @param $callback
     */
    public function each($callback) {
        array_walk($this->collection, $callback);
    }

    /**
     * @param null $callback
     * @return $this
     */
    public function sort($callback = null) {
        if($callback === null) {
            $this->collection = array_multisort($this->collection);
        } else {
            usort($this->collection, $callback);
        }

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function reduce($callback) {
        $this->collection = array_reduce($this->collection, $callback);

        return $this;
    }

    /**
     * @param $callback
     */
    public function single($callback) {
        $this->first($callback);
    }

    public function first($callback) {
        if(is_array($this->collection)) {
            $callback($this->collection[0]);
        } else {
            $callback($this->collection);
        }
    }
}