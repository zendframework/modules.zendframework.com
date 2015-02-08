<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Zend\View;
use ZfModule\View\Helper;

class ModuleViewTest extends PHPUnit_Framework_TestCase
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
                $this->equalTo('zf-module/helper/module-view.phtml'),
                $this->equalTo([
                    'module' => $module,
                    'button' => 'submit',
                ])
            )
        ;

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($module);
    }

    public function testInvokeAllowsSpecifyingButtonType()
    {
        $module = [
            'foo' => 'bar',
        ];

        $button = 'baz';

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-view.phtml'),
                $this->equalTo([
                    'module' => $module,
                    'button' => $button,
                ])
            )
        ;

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($module, $button);
    }
}
