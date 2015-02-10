<?php

namespace ApplicationTest\Integration\Util;

use PHPUnit_Framework_MockObject_Matcher_InvokedCount;
use PHPUnit_Framework_MockObject_MockBuilder;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager;

/**
 * @method ServiceManager\ServiceManager getApplicationServiceLocator()
 * @method PHPUnit_Framework_MockObject_MockBuilder getMockBuilder()
 * @method PHPUnit_Framework_MockObject_Matcher_InvokedCount once()
 * @method PHPUnit_Framework_MockObject_Matcher_InvokedCount any()
 */
trait AuthenticationTrait
{
    protected function notAuthenticated()
    {
        $authenticationService = $this->getMockBuilder(AuthenticationService::class)->getMock();

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

    protected function authenticatedAs($identity)
    {
        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $authenticationService
            ->expects($this->any())
            ->method('hasIdentity')
            ->willReturn(true)
        ;

        $authenticationService
            ->expects($this->any())
            ->method('getIdentity')
            ->willReturn($identity)
        ;

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
