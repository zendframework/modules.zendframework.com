<?php

namespace UserTest\Integration\Controller;

use ApplicationTest\Integration\Util\AuthenticationTrait;
use ApplicationTest\Integration\Util\Bootstrap;
use User\Entity\User;
use User\View\Helper\UserOrganizations;
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
    use AuthenticationTrait;

    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testIndexActionRedirectsIfNotAuthenticated()
    {
        $this->notAuthenticated();

        $this->dispatch('/user');

        $this->assertMatchedRouteName('scn-social-auth-user');

        $this->assertControllerName('zfcuser');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }

    public function testIndexActionSetsModulesIfAuthenticated()
    {
        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('currentUserModules')
            ->willReturn([])
        ;

        $serviceManager = $this->getApplicationServiceLocator();

        $serviceManager
            ->setAllowOverride(true)
            ->setService(
                Service\Module::class,
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

        $this->authenticatedAs(new User());

        $this->dispatch('/user');

        $this->assertMatchedRouteName('scn-social-auth-user');

        $this->assertControllerName('zfcuser');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
