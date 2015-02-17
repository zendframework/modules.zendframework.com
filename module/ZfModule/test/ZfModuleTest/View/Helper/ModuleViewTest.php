<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\View;
use ZfModule\Entity;
use ZfModule\View\Helper;

class ModuleViewTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeHandlesRepository()
    {
        $repository = $this->repository();

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-view.phtml'),
                $this->equalTo([
                    'owner' => $repository->owner->login,
                    'name' => $repository->name,
                    'createdAt' => $repository->created_at,
                    'url' => $repository->html_url,
                    'photoUrl' => $repository->owner->avatar_url,
                    'description' => $repository->description,
                    'button' => 'submit',
                ])
            )
        ;

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($repository);
    }

    public function testInvokeHandlesModule()
    {
        $module = $this->module();

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-view.phtml'),
                $this->equalTo([
                    'owner' => $module->getOwner(),
                    'name' => $module->getName(),
                    'createdAt' => $module->getCreatedAt(),
                    'url' => $module->getUrl(),
                    'photoUrl' => $module->getPhotoUrl(),
                    'description' => $module->getDescription(),
                    'button' => 'submit',
                ])
            )
        ;

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($module);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvokeDoesNotHandleAnythingElse()
    {
        $module = 'foo';

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($module);
    }

    public function testInvokeAllowsSpecifyingButtonType()
    {
        $module = $this->module();
        $button = 'baz';

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/module-view.phtml'),
                $this->logicalAnd(
                    $this->arrayHasKey('button'),
                    $this->callback(function ($values) use ($button) {
                        return $values['button'] === $button;
                    })
                )
            )
        ;

        $helper = new Helper\ModuleView();
        $helper->setView($view);

        $helper($module, $button);
    }

    /**
     * @return stdClass
     */
    private function repository()
    {
        $repository = new stdClass();

        $repository->name = 'foo';
        $repository->description = 'blah blah';
        $repository->created_at = '1970-01-01 00:00:00';
        $repository->html_url = 'http://www.example.org';

        $repository->owner = new stdClass();
        $repository->owner->login = 'suzie';
        $repository->owner->avatar_url = 'http://www.example.org/img/suzie.gif';

        return $repository;
    }

    /**
     * @return Entity\Module
     */
    private function module()
    {
        $module = new Entity\Module();

        $module->setName('foo');
        $module->setDescription('blah blah');
        $module->setCreatedAt('1970-01-01 00:00:00');
        $module->setUrl('http://www.example.org');
        $module->setOwner('suzie');
        $module->setPhotoUrl('http://www.example.org/img/suzie.gif');

        return $module;
    }
}
