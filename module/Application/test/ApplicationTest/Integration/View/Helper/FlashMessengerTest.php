<?php

namespace ApplicationTest\Integration\View\Helper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

/**
 * @covers Application\View\Helper\FlashMessenger
 */
class FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    public function testMultipleMessageTypesGetsRendered()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var $flashMessengerControllerPlugin \Zend\Mvc\Controller\Plugin\FlashMessenger */
        $flashMessengerControllerPlugin = $serviceManager->get('ControllerPluginManager')->get('FlashMessenger');

        $flashMessengerControllerPlugin->addMessage('FooMessage');
        $flashMessengerControllerPlugin->addSuccessMessage('FooSuccess');
        $flashMessengerControllerPlugin->addWarningMessage('FooWarning');
        $flashMessengerControllerPlugin->addErrorMessage('FooError');
        $flashMessengerControllerPlugin->addInfoMessage('FooInfo');

        /* @var \Application\View\Helper\FlashMessenger $flashMessengerViewHelper */
        $flashMessengerViewHelper = $serviceManager->get('ViewHelperManager')->get('FlashMessenger');

        $this->assertEquals(
            '<div class="alert alert-info"><span class="sr-only">Information</span>FooInfo</div>' .
            '<div class="alert alert-danger"><span class="sr-only">Error</span>FooError</div>' .
            '<div class="alert alert-success"><span class="sr-only">Success</span>FooSuccess</div>' .
            '<div class="alert alert-info"><span class="sr-only">Message</span>FooMessage</div>' .
            '<div class="alert alert-warning"><span class="sr-only">Warning</span>FooWarning</div>',
            $flashMessengerViewHelper->renderCurrent()
        );
    }

    public function testCustomNamespaceMessageGetsRenderedAsInformationMessage()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var $flashMessengerControllerPlugin \Zend\Mvc\Controller\Plugin\FlashMessenger */
        $flashMessengerControllerPlugin = $serviceManager->get('ControllerPluginManager')->get('FlashMessenger');
        $flashMessengerControllerPlugin->setNamespace('FooBar');

        $flashMessengerControllerPlugin->addMessage('FooMessage');

        /* @var \Application\View\Helper\FlashMessenger $flashMessengerViewHelper */
        $flashMessengerViewHelper = $serviceManager->get('ViewHelperManager')->get('FlashMessenger');

        $this->assertEquals(
            '<div class="alert alert-info"><span class="sr-only">Message</span>FooMessage</div>',
            $flashMessengerViewHelper->renderCurrent('FooBar')
        );
    }
}
