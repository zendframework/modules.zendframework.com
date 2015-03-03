<?php

namespace ApplicationTest\Mock;

class StringCastable
{
    private $value;

    public function __construct($string)
    {
        $this->value = $string;
    }

    public function __toString()
    {
        return $this->value;
    }
}
