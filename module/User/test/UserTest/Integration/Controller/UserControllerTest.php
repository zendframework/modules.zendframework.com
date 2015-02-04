<?php

namespace UserTest\Integration\Controller;

use ApplicationTest\Integration\Util\Bootstrap;
use Zend\Authentication\AuthenticationService;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * @coversNothing
 */
class UserControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testIndexActionRedirectsIfNotAuthenticated()
    {
        $authenticationService = $this->getMockBuilder(AuthenticationService::class)->getMock();

        $authenticationService
            ->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(false)
        ;

        $serviceManager = $this->getApplicationServiceLocator();

        $serviceManager
            ->setAllowOverride(true)
            ->setService(
                'zfcuser_auth_service',
                $authenticationService
            )
        ;

        $this->dispatch('/user');

        $this->assertControllerName('zfcuser');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }
}
