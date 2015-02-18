<?php

namespace ZfModuleTest\Integration\Mapper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use ZfModule\Mapper;

/**
 * @coversNothing
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    public function testCanRetrieveService()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Mapper\Module::class,
            $serviceManager->get(Mapper\Module::class)
        );
    }
}
