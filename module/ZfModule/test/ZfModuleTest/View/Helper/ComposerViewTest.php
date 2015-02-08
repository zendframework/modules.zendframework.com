<?php

namespace ZfModuleTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Zend\View;
use ZfModule\View\Helper;

class ComposerViewTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScript()
    {
        $composerConf = '{"foo":"bar"}';

        $view = $this->getMockForAbstractClass(View\Renderer\RendererInterface::class);

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('zf-module/helper/composer-view.phtml'),
                $this->equalTo([
                    'composerConf' => json_decode($composerConf, true),
                ])
            )
        ;

        $helper = new Helper\ComposerView();
        $helper->setView($view);

        $helper($composerConf);
    }
}
