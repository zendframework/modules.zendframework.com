<?php

namespace ApplicationTest\Integration\View\Helper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

class FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    public function testMultipleMessageTypesGetsRendered()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /** @var \Zend\Mvc\Controller\Plugin\FlashMessenger $flashMessengerPlugin */
        $flashMessengerControllerPlugin = $serviceManager->get('ControllerPluginManager')->get('FlashMessenger');

        /* @var \Application\View\Helper\FlashMessenger $flashMessengerHelper */
        $flashMessengerViewHelper = $serviceManager->get('ViewHelperManager')->get('FlashMessenger');

        $flashMessengerControllerPlugin->addMessage('FooMessage');
        $flashMessengerControllerPlugin->addSuccessMessage('FooSuccess');
        $flashMessengerControllerPlugin->addWarningMessage('FooWarning');
        $flashMessengerControllerPlugin->addErrorMessage('FooError');
        $flashMessengerControllerPlugin->addInfoMessage('FooInfo');

        $this->assertEquals(
            '<div class="alert alert-info"><span class="sr-only">Information</span>FooInfo</div>' .
            '<div class="alert alert-danger"><span class="sr-only">Error</span>FooError</div>' .
            '<div class="alert alert-success"><span class="sr-only">Success</span>FooSuccess</div>' .
            '<div class="alert alert-info"><span class="sr-only">Message</span>FooMessage</div>' .
            '<div class="alert alert-warning"><span class="sr-only">Warning</span>FooWarning</div>',
            $flashMessengerViewHelper->render()
        );
    }

    public function testCustomNamespaceMessageGetsRenderedAsInformationMessage()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /** @var \Zend\Mvc\Controller\Plugin\FlashMessenger $flashMessengerPlugin */
        $flashMessengerControllerPlugin = $serviceManager->get('ControllerPluginManager')->get('FlashMessenger');
        $flashMessengerControllerPlugin->setNamespace('FooBar');

        /* @var \Application\View\Helper\FlashMessenger $flashMessengerHelper */
        $flashMessengerViewHelper = $serviceManager->get('ViewHelperManager')->get('FlashMessenger');
        $flashMessengerControllerPlugin->addMessage('FooMessage');

        $this->assertEquals(
            '<div class="alert alert-info"><span class="sr-only">Message</span>FooMessage</div>',
            $flashMessengerViewHelper->render('FooBar')
        );
    }
}
