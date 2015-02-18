<?php

namespace ZfModuleTest\Integration\Service;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use ZfModule\Service;

/**
 * @coversNothing
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    public function testCanRetrieveService()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Service\Module::class,
            $serviceManager->get(Service\Module::class)
        );
    }
}
