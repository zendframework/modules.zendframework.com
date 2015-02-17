<?php

namespace ZfModuleTest\Integration\View\Helper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\View\HelperPluginManager;
use ZfModule\View\Helper;

/**
 * @coversNothing
 */
class RepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testCanRetrieveService()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var HelperPluginManager $helperPluginManager */
        $helperPluginManager = $serviceManager->get('ViewHelperManager');

        $this->assertInstanceOf(
            Helper\Repository::class,
            $helperPluginManager->get('repository')
        );
    }
}
