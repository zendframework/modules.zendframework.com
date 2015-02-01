<?php

namespace ApplicationTest\Integration\Service;

use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use Psr\Log;
use PHPUnit_Framework_TestCase;

class LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Log\LoggerInterface::class,
            $serviceManager->get(Log\LoggerInterface::class)
        );
    }
}
