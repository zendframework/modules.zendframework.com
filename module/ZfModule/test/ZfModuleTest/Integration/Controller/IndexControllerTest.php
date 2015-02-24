<?php

namespace ZfModuleTest\Integration\Controller;

use Application\Service\RepositoryRetriever;
use ApplicationTest\Integration\Util\AuthenticationTrait;
use ApplicationTest\Integration\Util\Bootstrap;
use EdpGithub\Collection;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use stdClass;
use Zend\Http;
use Zend\Mvc;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\View;
use ZfcUser\Entity\User;
use ZfModule\Controller;
use ZfModule\Entity;
use ZfModule\Mapper;
use ZfModule\Service;

/**
 * @method Mvc\Application getApplication()
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
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

        $this->dispatch('/module');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }

    public function testIndexActionFetches100MostRecentlyUpdatedUserRepositories()
    {
        $this->authenticatedAs(new User());

        $repositoryCollection = $this->repositoryCollectionMock();

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getAuthenticatedUserRepositories')
            ->with($this->equalTo([
                'type' => 'all',
                'per_page' => 100,
                'sort' => 'updated',
                'direction' => 'desc',
            ]))
            ->willReturn($repositoryCollection)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch('/module');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }

    public function testIndexActionRendersValidModulesOnly()
    {
        $this->authenticatedAs(new User());

        $validModule = $this->validModule();

        $nonModule = $this->nonModule();
        $registeredModule = $this->registeredModule();

        $repositories = [
            $validModule,
            $nonModule,
            $registeredModule,
            $this->forkedModule(),
            $this->moduleWithoutPushPermissions(),
        ];

        $repositoryCollection = $this->repositoryCollectionMock($repositories);

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getAuthenticatedUserRepositories')
            ->willReturn($repositoryCollection)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->any())
            ->method('isModule')
            ->willReturnCallback(function ($repository) use ($nonModule) {
                if ($repository !== $nonModule) {
                    return true;
                }

                return false;
            })
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->any())
            ->method('findByName')
            ->willReturnCallback(function ($name) use ($registeredModule) {
                if ($name === $registeredModule->name) {
                    return true;
                }

                return null;
            })
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Service\Module::class,
                $moduleService
            )
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $this->dispatch('/module');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);

        /* @var Mvc\Application $application */
        $viewModel = $this->getApplication()->getMvcEvent()->getViewModel();

        $this->assertTrue($viewModel->terminate());
        $this->assertSame('zf-module/index/index', $viewModel->getTemplate());

        $viewVariable = $viewModel->getVariable('repositories');

        $this->assertInternalType('array', $viewVariable);
        $this->assertCount(1, $viewVariable);
        $this->assertSame($validModule, $viewVariable[0]);
    }

    public function testOrganizationActionRedirectsIfNotAuthenticated()
    {
        $this->notAuthenticated();

        $vendor = 'foo';

        $url = sprintf(
            '/module/list/%s',
            $vendor
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('organization');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }

    public function testOrganizationActionFetches100MostRecentlyUpdatedRepositoriesWhenNoOwnerIsSpecified()
    {
        $this->authenticatedAs(new User());

        $repositoryCollection = $this->repositoryCollectionMock();

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositories')
            ->with(
                $this->equalTo(null),
                $this->equalTo([
                    'per_page' => 100,
                    'sort' => 'updated',
                    'direction' => 'desc',
                ]
            ))
            ->willReturn($repositoryCollection)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch('/module/list');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('organization');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }

    public function testOrganizationActionFetches100MostRecentlyUpdatedRepositoriesWithOwnerSpecified()
    {
        $this->authenticatedAs(new User());

        $repositoryCollection = $this->repositoryCollectionMock();

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $vendor = 'suzie';

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositories')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo([
                    'per_page' => 100,
                    'sort' => 'updated',
                    'direction' => 'desc',
                ]
            ))
            ->willReturn($repositoryCollection)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $url = sprintf(
            '/module/list/%s',
            $vendor
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('organization');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }

    public function testOrganizationActionRendersValidModulesOnly()
    {
        $this->authenticatedAs(new User());

        $validModule = $this->validModule();

        $nonModule = $this->nonModule();
        $registeredModule = $this->registeredModule();

        $repositories = [
            $validModule,
            $nonModule,
            $registeredModule,
            $this->forkedModule(),
            $this->moduleWithoutPushPermissions(),
        ];

        $repositoryCollection = $this->repositoryCollectionMock($repositories);

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $vendor = 'suzie';

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositories')
            ->willReturn($repositoryCollection)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->any())
            ->method('isModule')
            ->willReturnCallback(function ($repository) use ($nonModule) {
                if ($repository !== $nonModule) {
                    return true;
                }

                return false;
            })
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->any())
            ->method('findByName')
            ->willReturnCallback(function ($name) use ($registeredModule) {
                if ($name === $registeredModule->name) {
                    return true;
                }

                return null;
            })
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Service\Module::class,
                $moduleService
            )
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $url = sprintf(
            '/module/list/%s',
            $vendor
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('organization');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);

        /* @var Mvc\Application $application */
        $viewModel = $this->getApplication()->getMvcEvent()->getViewModel();

        $this->assertTrue($viewModel->terminate());
        $this->assertSame('zf-module/index/index.phtml', $viewModel->getTemplate());

        $viewVariable = $viewModel->getVariable('repositories');

        $this->assertInternalType('array', $viewVariable);
        $this->assertCount(1, $viewVariable);
        $this->assertSame($validModule, $viewVariable[0]);
    }

    public function testAddActionRedirectsIfNotAuthenticated()
    {
        $this->notAuthenticated();

        $this->dispatch('/module/add');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }

    /**
     * @dataProvider providerNotPost
     *
     * @param string $method
     */
    public function testAddActionThrowsUnexpectedValueExceptionIfNotPostedTo($method)
    {
        $this->authenticatedAs(new User());

        $this->dispatch(
            '/module/add',
            $method
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame('Something went wrong with the post values of the request...', $exception->getMessage());
    }

    /**
     * @return array
     */
    public function providerNotPost()
    {
        return [
            [
                Http\Request::METHOD_GET,
            ],
            [
                Http\Request::METHOD_PUT,
            ],
            [
                Http\Request::METHOD_DELETE,
            ],
        ];
    }

    public function testAddActionThrowsRuntimeExceptionIfUnableToFetchRepositoryMetaData()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch(
            '/module/add',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\RuntimeException::class, $exception);
        $this->assertSame(
            'Not able to fetch the repository from GitHub due to an unknown error.',
            $exception->getMessage()
        );
    }

    /**
     * @dataProvider providerRepositoryWithInsufficientPrivileges
     *
     * @param stdClass $repository
     */
    public function testAddActionThrowsUnexpectedValueExceptionWhenRepositoryHasInsufficientPrivileges($repository)
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($repository)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch(
            '/module/add',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame(
            'You have no permission to add this module. The reason might be that you are ' .
            'neither the owner nor a collaborator of this repository.',
            $exception->getMessage()
        );
    }

    /**
     * @return \Generator
     */
    public function providerRepositoryWithInsufficientPrivileges()
    {
        yield [
            $this->forkedModule(),
        ];

        yield [
            $this->moduleWithoutPushPermissions(),
        ];
    }

    public function testAddActionThrowsUnexpectedValueExceptionWhenRepositoryIsNotAModule()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $nonModule = $this->nonModule();

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($nonModule)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('isModule')
            ->with($this->equalTo($nonModule))
            ->willReturn(false)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Service\Module::class,
                $moduleService
            )
        ;

        $this->dispatch(
            '/module/add',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame(
            sprintf(
                '%s is not a Zend Framework Module',
                $nonModule->name
            ),
            $exception->getMessage()
        );
    }

    public function testAddActionRegistersRepositoryIfPermissionsAreSufficientAndItIsAModule()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $validModule = $this->validModule();

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($validModule)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('isModule')
            ->with($this->equalTo($validModule))
            ->willReturn(true)
        ;

        $moduleService
            ->expects($this->once())
            ->method('register')
            ->with($this->equalTo($validModule))
            ->willReturn(new Entity\Module())
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Service\Module::class,
                $moduleService
            )
        ;

        $this->dispatch(
            '/module/add',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('add');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user');
    }

    public function testRemoveActionRedirectsIfNotAuthenticated()
    {
        $this->notAuthenticated();

        $this->dispatch('/module/remove');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user/login');
    }

    /**
     * @dataProvider providerNotPost
     *
     * @param string $method
     */
    public function testRemoveActionThrowsUnexpectedValueExceptionIfNotPostedTo($method)
    {
        $this->authenticatedAs(new User());

        $this->dispatch(
            '/module/remove',
            $method
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame('Something went wrong with the post values of the request...', $exception->getMessage());
    }

    public function testRemoveActionThrowsRuntimeExceptionIfUnableToFetchRepositoryMetaData()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch(
            '/module/remove',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\RuntimeException::class, $exception);
        $this->assertSame(
            'Not able to fetch the repository from GitHub due to an unknown error.',
            $exception->getMessage()
        );
    }

    /**
     * @dataProvider providerRepositoryWithInsufficientPrivileges
     *
     * @param stdClass $repository
     */
    public function testRemoveActionThrowsUnexpectedValueExceptionWhenRepositoryHasInsufficientPrivileges($repository)
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($repository)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch(
            '/module/remove',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame(
            'You have no permission to remove this module. The reason might be that you are ' .
            'neither the owner nor a collaborator of this repository.',
            $exception->getMessage()
        );
    }

    public function testRemoveActionThrowsUnexpectedValueExceptionWhenRepositoryNotPreviouslyRegistered()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $unregisteredModule = $this->unregisteredModule();

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($unregisteredModule)
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByUrl')
            ->with($this->equalTo($unregisteredModule->html_url))
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $this->dispatch(
            '/module/remove',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_500);

        /* @var View\Model\ViewModel  $result */
        $result = $this->getApplication()->getMvcEvent()->getResult();

        /* @var Exception $exception */
        $exception = $result->getVariable('exception');

        $this->assertInstanceOf(Controller\Exception\UnexpectedValueException::class, $exception);
        $this->assertSame(
            sprintf(
                '%s was not found',
                $unregisteredModule->name
            ),
            $exception->getMessage()
        );
    }

    public function testRemoveActionDeletesModuleIfPermissionsAreSufficientAndItHasBeenRegistered()
    {
        $this->authenticatedAs(new User());

        $vendor = 'suzie';
        $name = 'foo';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $registeredModule = $this->registeredModule();

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($registeredModule)
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $module = new Entity\Module();

        $moduleMapper
            ->expects($this->once())
            ->method('findByUrl')
            ->with($this->equalTo($registeredModule->html_url))
            ->willReturn($module)
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($module))
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $this->dispatch(
            '/module/remove',
            Http\Request::METHOD_POST,
            [
                'repo' => $name,
                'owner' => $vendor,
            ]
        );

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('remove');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_302);

        $this->assertRedirectTo('/user');
    }

    public function testViewActionSetsHttp404ResponseCodeIfModuleNotFound()
    {
        $vendor = 'foo';
        $name = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($name))
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $url = sprintf(
            '/%s/%s',
            $vendor,
            $name
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('not-found');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_404);
    }

    public function testViewActionSetsHttp404ResponseCodeIfRepositoryMetaDataNotFound()
    {
        $vendor = 'foo';
        $name = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($name))
            ->willReturn(new Entity\Module())
        ;

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $url = sprintf(
            '/%s/%s',
            $vendor,
            $name
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('not-found');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_404);
    }

    public function testViewActionCanBeAccessed()
    {
        $vendor = 'foo';
        $name = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($name))
            ->willReturn(new Entity\Module())
        ;

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn(new stdClass())
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
            ->setService(
                RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $url = sprintf(
            '/%s/%s',
            $vendor,
            $name
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('view');
    }

    /**
     * @link http://stackoverflow.com/a/15907250
     *
     * @param array $repositories
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function repositoryCollectionMock(array $repositories = [])
    {
        $data = new stdClass();
        $data->array = $repositories;
        $data->position = 0;

        $repositoryCollection = $this->getMockBuilder(Collection\RepositoryCollection::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('rewind')
            ->willReturnCallback(function () use ($data) {
                $data->position = 0;
            })
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('current')
            ->willReturnCallback(function () use ($data) {
                return $data->array[$data->position];
            })
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('key')
            ->willReturnCallback(function () use ($data) {
                return $data->position;
            })
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('next')
            ->willReturnCallback(function () use ($data) {
                $data->position++;
            })
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('valid')
            ->willReturnCallback(function () use ($data) {
                return isset($data->array[$data->position]);
            })
        ;

        $repositoryCollection
            ->expects($this->any())
            ->method('count')
            ->willReturnCallback(function () use ($data) {
                return count($data->array);
            })
        ;

        return $repositoryCollection;
    }

    /**
     * @return stdClass
     */
    private function validModule()
    {
        $repository = new stdClass();

        $repository->name = 'foo';
        $repository->description = 'blah blah';
        $repository->fork = false;
        $repository->created_at = '1970-01-01 00:00:00';
        $repository->html_url = 'http://www.example.org';

        $repository->owner = new stdClass();
        $repository->owner->login = 'suzie';
        $repository->owner->avatar_url = 'http://www.example.org/img/suzie.gif';

        $repository->permissions = new stdClass();
        $repository->permissions->push = true;

        return $repository;
    }

    /**
     * @return stdClass
     */
    private function nonModule()
    {
        $repository = $this->validModule();

        $repository->name = 'non-module';

        return $repository;
    }

    /**
     * @return stdClass
     */
    private function forkedModule()
    {
        $repository = $this->validModule();

        $repository->name = 'forked-module';
        $repository->fork = true;

        return $repository;
    }

    /**
     * @return stdClass
     */
    private function moduleWithoutPushPermissions()
    {
        $repository = $this->validModule();

        $repository->name = 'module-without-push-permissions';
        $repository->permissions->push = false;

        return $repository;
    }

    /**
     * @return stdClass
     */
    private function registeredModule()
    {
        $repository = $this->validModule();

        $repository->name = 'registered-module';

        return $repository;
    }

    /**
     * @return stdClass
     */
    private function unregisteredModule()
    {
        $repository = $this->validModule();

        $repository->name = 'unregistered-module';

        return $repository;
    }
}
