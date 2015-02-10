<?php

namespace ApplicationTest\Integration\Util;

use PHPUnit_Framework_MockObject_Matcher_InvokedCount;
use PHPUnit_Framework_MockObject_MockBuilder;
use Zend\Authentication;
use Zend\ServiceManager;

/**
 * @method ServiceManager\ServiceManager getApplicationServiceLocator
 * @method PHPUnit_Framework_MockObject_MockBuilder getMockBuilder()
 * @method PHPUnit_Framework_MockObject_Matcher_InvokedCount once()
 */
trait AuthenticationTrait
{
    protected function notAuthenticated()
    {
        $authenticationService = $this->getMockBuilder(Authentication\AuthenticationService::class)
            ->getMock();

        $authenticationService
            ->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(false);

        $serviceManager = $this->getApplicationServiceLocator();

        $serviceManager
            ->setAllowOverride(true)
            ->setService(
                'zfcuser_auth_service',
                $authenticationService
            )
        ;
    }
}
