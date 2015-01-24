<?php

namespace ApplicationTest\Integration\View\Helper;

use Application\View\Helper\SanitizeHtml;
use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

class SanitizeHtmlTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var \Zend\View\HelperPluginManager $viewHelperManager */
        $viewHelperManager = $serviceManager->get('ViewHelperManager');

        $this->assertInstanceOf(
            SanitizeHtml::class,
            $viewHelperManager->get('sanitizeHtml')
        );
    }

    public function testHtmlGetsCleaned()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var \Zend\View\HelperPluginManager $viewHelperManager */
        $viewHelperManager = $serviceManager->get('ViewHelperManager');

        /* @var \Application\View\Helper\SanitizeHtml $sanitizeHtmlHelper */
        $sanitizeHtmlHelper = $viewHelperManager->get('sanitizeHtml');

        $dirtyHtml = 'Foo<script>alert(\'I_WILL_BE_REMOVED\');</script>Bar';
        $this->assertEquals('FooBar', $sanitizeHtmlHelper->__invoke($dirtyHtml));
    }
}
