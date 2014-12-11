<?php

namespace UserTest\Integration\View\Helper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

class NewUsersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return ServiceManager
     */
    private function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    public function testCanCreateService()
    {
        $helperPluginManager = $this->getServiceManager()->get('ViewHelperManager');

        $this->assertInstanceOf(
            'User\View\Helper\NewUsers',
            $helperPluginManager->get('newUsers')
        );
    }
}
