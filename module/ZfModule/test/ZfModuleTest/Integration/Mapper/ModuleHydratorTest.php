<?php

namespace ZfModuleTest\Integration\Mapper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use ZfModule\Mapper;

/**
 * @coversNothing
 */
class ModuleHydratorTest extends PHPUnit_Framework_TestCase
{
    public function testCanRetrieveService()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Mapper\ModuleHydrator::class,
            $serviceManager->get(Mapper\ModuleHydrator::class)
        );
    }
}
