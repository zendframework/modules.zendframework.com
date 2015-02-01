<?php

namespace ApplicationTest\Integration\Service;

use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

/**
 * @covers Application\Service\ErrorHandlingService
 */
class ErrorHandlingServiceTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Service\ErrorHandlingService::class,
            $serviceManager->get(Service\ErrorHandlingService::class)
        );
    }
}
