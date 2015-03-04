<?php

namespace Application\Entity;

class Repository
{
    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $owner
     * @param string $name
     */
    public function __construct($owner, $name)
    {
        $this->owner = (string) $owner;
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function owner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
