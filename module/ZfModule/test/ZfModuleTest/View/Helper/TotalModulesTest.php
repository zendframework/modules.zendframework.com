<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use ReflectionObject;
use ZfModule\Mapper;
use ZfModule\View\Helper;

class TotalModulesTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeReturnsNumberOfTotalModules()
    {
        $totalModules = 9000;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('getTotal')
            ->willReturn($totalModules)
        ;

        $helper = new Helper\TotalModules($moduleMapper);

        $this->assertSame($totalModules, $helper());
    }

    public function testInvokeDoesNotFetchTotalModulesWhenAlreadyFetched()
    {
        $totalModules = 9000;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->never())
            ->method('getTotal')
        ;

        $helper = new Helper\TotalModules($moduleMapper);

        $reflection = new ReflectionObject($helper);

        $property = $reflection->getProperty('total');

        $property->setAccessible(true);
        $property->setValue($helper, $totalModules);

        $this->assertSame($totalModules, $helper());
    }
}
