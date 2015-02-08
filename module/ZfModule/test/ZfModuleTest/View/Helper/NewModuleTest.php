<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Zend\View;
use ZfModule\Mapper;
use ZfModule\View\Helper;

class NewModuleTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScriptWithTenMostRecentModules()
    {
        $modules = [
            'foo',
            'bar',
            'baz',
        ];

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo(10),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn($modules)
        ;

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/new-module'),
                $this->equalTo([
                    'modules' => $modules,
                ])
            )
        ;

        $helper = new Helper\NewModule($moduleMapper);
        $helper->setView($view);

        $helper();
    }
}
