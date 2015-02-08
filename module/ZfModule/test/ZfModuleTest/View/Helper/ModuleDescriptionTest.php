<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Zend\View;
use ZfModule\View\Helper;

class ModuleDescriptionTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScript()
    {
        $module = [
            'foo' => 'bar',
        ];

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-description.phtml'),
                $this->equalTo([
                    'module' => $module,
                ])
            )
        ;

        $helper = new Helper\ModuleDescription();
        $helper->setView($view);

        $helper($module);
    }
}
