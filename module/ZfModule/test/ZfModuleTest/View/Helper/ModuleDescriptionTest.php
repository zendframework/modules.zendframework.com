<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\View;
use ZfModule\View\Helper;

class ModuleDescriptionTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScript()
    {
        $module = new stdClass();
        $module->owner = new stdClass();
        $module->owner->login = 'foo';
        $module->owner->avatar_url = 'http://www.example.org/john.gif';
        $module->name = 'bar';
        $module->created_at = '1970-01-01 00:00:00';
        $module->html_url = 'http://www.example.org';
        $module->description = 'blah blah blah';

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-description.phtml'),
                $this->equalTo([
                    'owner' => $module->owner->login,
                    'name' => $module->name,
                    'createdAt' => $module->created_at,
                    'url' => $module->html_url,
                    'photoUrl' => $module->owner->avatar_url,
                    'description' => $module->description,
                ])
            )
        ;

        $helper = new Helper\ModuleDescription();
        $helper->setView($view);

        $helper($module);
    }
}
