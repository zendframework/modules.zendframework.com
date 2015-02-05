<?php

namespace UserTest\Integration\Controller;

use ApplicationTest\Integration\Util\Bootstrap;
use User\Entity\User;
use User\View\Helper\UserOrganizations;
use Zend\Authentication\AuthenticationService;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\View;
use ZfModule\Service;
use ZfModule\View\Helper\TotalModules;

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

    public function testIndexActionSetsModulesIfAuthenticated()
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
            ->willReturn(new User())
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('listUserModules')
            ->willReturn([])
        ;

        $serviceManager = $this->getApplicationServiceLocator();

        $serviceManager
            ->setAllowOverride(true)
            ->setService(
                'zfcuser_auth_service',
                $authenticationService
            )
            ->setService(
                'zfmodule_service_module',
                $moduleService
            )
        ;

        $userOrganizations = $this->getMockBuilder(UserOrganizations::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $userOrganizations
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn('foo')
        ;

        $totalModules = $this->getMockBuilder(TotalModules::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $totalModules
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn('foo')
        ;

        /* @var View\HelperPluginManager $viewHelperManager */
        $viewHelperManager = $serviceManager->get('ViewHelperManager');

        $viewHelperManager
            ->setAllowOverride(true)
            ->setService(
                'userOrganizations',
                $userOrganizations
            )
            ->setService(
                'totalModules',
                $totalModules
            )
        ;

        $this->dispatch('/user');

        $this->assertControllerName('zfcuser');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
