<?php

namespace ZfModuleTest\Mock\Collection;

use Iterator;

class RepositoryCollection implements Iterator
{
    /**
     * @var array
     */
    private $repositories;

    public function __construct(array $repositories = [])
    {
        $this->repositories = $repositories;
    }

    public function current()
    {
        return current($this->repositories);
    }

    public function next()
    {
        next($this->repositories);
    }

    public function key()
    {
        return key($this->repositories);
    }

    public function valid()
    {
        $key = key($this->repositories);

        if (null === $key || false === $key) {
            return false;
        }

        return true;
    }

    public function rewind()
    {
        reset($this->repositories);
    }
}
