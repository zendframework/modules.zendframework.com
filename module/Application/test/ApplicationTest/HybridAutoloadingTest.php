<?php

namespace ApplicationTest;

use Hybrid_Providers_GitHub;
use PHPUnit_Framework_TestCase;

class HybridAutoloadingTest extends PHPUnit_Framework_TestCase
{
    public function testCanLoadClass()
    {
        $this->assertTrue(class_exists('Hybrid_Providers_GitHub'));
    }
}
