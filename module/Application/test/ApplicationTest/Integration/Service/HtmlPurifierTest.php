<?php

namespace ApplicationTest\Integration\Service;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

class HtmlPurifierTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            \HTMLPurifier::class,
            $serviceManager->get(\HTMLPurifier::class)
        );
    }
}
