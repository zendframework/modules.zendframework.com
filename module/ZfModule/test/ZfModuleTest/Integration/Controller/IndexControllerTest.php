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

    public function testIndexActionFiltersOutUserRepositoriesWhichAreNeitherModulesNorAddedNorEligibleOtherwise()
    {
        $this->authenticatedAs(new User());

        $repositories = [];

        $module = new stdClass();
        $module->name = 'foo';
        $module->description = 'blah blah';
        $module->fork = false;
        $module->created_at = '1970-01-01 00:00:00';
        $module->html_url = 'http://www.example.org';
        $module->owner = new stdClass();
        $module->owner->login = 'johndoe';
        $module->owner->avatar_url = 'johndoe';
        $module->permissions = new stdClass();
        $module->permissions->push = true;

        array_push($repositories, $module);

        $nonModule = new stdClass();
        $nonModule->name = 'bar';
        $nonModule->fork = false;
        $nonModule->permissions = new stdClass();
        $nonModule->permissions->push = true;

        array_push($repositories, $nonModule);

        $forkedModule = new stdClass();
        $forkedModule->name = 'baz';
        $forkedModule->fork = true;
        $forkedModule->permissions = new stdClass();
        $forkedModule->permissions->push = true;

        array_push($repositories, $forkedModule);

        $moduleWithoutPushPermissions = new stdClass();
        $moduleWithoutPushPermissions->name = 'qux';
        $moduleWithoutPushPermissions->fork = false;
        $moduleWithoutPushPermissions->permissions = new stdClass();
        $moduleWithoutPushPermissions->permissions->push = false;

        array_push($repositories, $moduleWithoutPushPermissions);

        $registeredModule = new stdClass();
        $registeredModule->name = 'vqz';
        $registeredModule->fork = false;
        $registeredModule->permissions = new stdClass();
        $registeredModule->permissions->push = true;

        array_push($repositories, $registeredModule);

        $repositoryCollection = $this->repositoryCollectionMock($repositories);

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

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->any())
            ->method('isModule')
            ->willReturnCallback(function ($module) use ($nonModule) {
                if ($module === $nonModule) {
                    return false;
                }

                return true;
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
                'zfmodule_service_module',
                $moduleService
            )
            ->setService(
                'zfmodule_mapper_module',
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
        $this->assertSame($module, $viewVariable[0]);
    }

    public function testOrganizationActionRedirectsIfNotAuthenticated()
    {
        $this->notAuthenticated();

        $owner = 'foo';

        $url = sprintf(
            '/module/list/%s',
            $owner
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

        $owner = 'johndoe';

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositories')
            ->with(
                $this->equalTo($owner),
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
            $owner
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('organization');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }

    public function testOrganizationActionFiltersOutUserRepositoriesWhichAreNeitherModulesNorAddedNorEligibleOtherwise()
    {
        $this->authenticatedAs(new User());

        $repositories = [];

        $module = new stdClass();
        $module->name = 'foo';
        $module->description = 'blah blah';
        $module->fork = false;
        $module->created_at = '1970-01-01 00:00:00';
        $module->html_url = 'http://www.example.org';
        $module->owner = new stdClass();
        $module->owner->login = 'johndoe';
        $module->owner->avatar_url = 'johndoe';
        $module->permissions = new stdClass();
        $module->permissions->push = true;

        array_push($repositories, $module);

        $nonModule = new stdClass();
        $nonModule->name = 'bar';
        $nonModule->fork = false;
        $nonModule->permissions = new stdClass();
        $nonModule->permissions->push = true;

        array_push($repositories, $nonModule);

        $forkedModule = new stdClass();
        $forkedModule->name = 'baz';
        $forkedModule->fork = true;
        $forkedModule->permissions = new stdClass();
        $forkedModule->permissions->push = true;

        array_push($repositories, $forkedModule);

        $moduleWithoutPushPermissions = new stdClass();
        $moduleWithoutPushPermissions->name = 'qux';
        $moduleWithoutPushPermissions->fork = false;
        $moduleWithoutPushPermissions->permissions = new stdClass();
        $moduleWithoutPushPermissions->permissions->push = false;

        array_push($repositories, $moduleWithoutPushPermissions);

        $registeredModule = new stdClass();
        $registeredModule->name = 'vqz';
        $registeredModule->fork = false;
        $registeredModule->permissions = new stdClass();
        $registeredModule->permissions->push = true;

        array_push($repositories, $registeredModule);

        $repositoryCollection = $this->repositoryCollectionMock($repositories);

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $owner = 'johndoe';

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositories')
            ->with(
                $this->equalTo($owner),
                $this->equalTo([
                    'per_page' => 100,
                    'sort' => 'updated',
                    'direction' => 'desc',
                ])
            )
            ->willReturn($repositoryCollection)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->any())
            ->method('isModule')
            ->willReturnCallback(function ($module) use ($nonModule) {
                if ($module === $nonModule) {
                    return false;
                }

                return true;
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
                'zfmodule_service_module',
                $moduleService
            )
            ->setService(
                'zfmodule_mapper_module',
                $moduleMapper
            )
        ;

        $url = sprintf(
            '/module/list/%s',
            $owner
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
        $this->assertSame($module, $viewVariable[0]);
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

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
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
                'repo' => $repository,
                'owner' => $owner,
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
            'Not able to fetch the repository from github due to an unknown error.',
            $exception->getMessage()
        );
    }

    /**
     * @dataProvider providerModuleWithInsufficientPrivileges
     *
     * @param stdClass $module
     */
    public function testAddActionThrowsUnexpectedValueExceptionWhenRepositoryIsForkOrUserHasNoPushPermissions($module)
    {
        $this->authenticatedAs(new User());

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
            )
            ->willReturn($module)
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
                'repo' => $repository,
                'owner' => $owner,
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
    public function providerModuleWithInsufficientPrivileges()
    {
        $module = new stdClass();
        $module->permissions = new stdClass();

        $module->fork = true;
        $module->permissions->push = true;

        yield [
            $module,
        ];

        $module->fork = false;
        $module->permissions->push = false;

        yield [
            $module,
        ];
    }

    public function testAddActionThrowsUnexpectedValueExceptionWhenRepositoryIsNotAModule()
    {
        $this->authenticatedAs(new User());

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $module = new stdClass();
        $module->name = 'foo';
        $module->fork = false;
        $module->permissions = new stdClass();
        $module->permissions->push = true;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
            )
            ->willReturn($module)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('isModule')
            ->with($this->equalTo($module))
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
                'repo' => $repository,
                'owner' => $owner,
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
                $module->name
            ),
            $exception->getMessage()
        );
    }

    public function testAddActionRegistersRepositoryIfPermissionsAreSufficientAndItIsAModule()
    {
        $this->authenticatedAs(new User());

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $module = new stdClass();
        $module->name = 'foo';
        $module->fork = false;
        $module->permissions = new stdClass();
        $module->permissions->push = true;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
            )
            ->willReturn($module)
        ;

        $moduleService = $this->getMockBuilder(Service\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleService
            ->expects($this->once())
            ->method('isModule')
            ->with($this->equalTo($module))
            ->willReturn(true)
        ;

        $moduleService
            ->expects($this->once())
            ->method('register')
            ->with($this->equalTo($module))
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
                'repo' => $repository,
                'owner' => $owner,
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

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
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
                'repo' => $repository,
                'owner' => $owner,
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
            'Not able to fetch the repository from github due to an unknown error.',
            $exception->getMessage()
        );
    }

    /**
     * @dataProvider providerModuleWithInsufficientPrivileges
     *
     * @param stdClass $module
     */
    public function testRemoveActionThrowsUnexpectedValueExceptionWhenRepositoryIsForkOrUserHasNoPushPermissions($module)
    {
        $this->authenticatedAs(new User());

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
            )
            ->willReturn($module)
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
                'repo' => $repository,
                'owner' => $owner,
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

        $repository = 'foo';
        $owner = 'johndoe';

        $repositoryRetriever = $this->getMockBuilder(RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $module = new stdClass();
        $module->name = 'foo';
        $module->html_url = 'http://example.org';
        $module->fork = false;
        $module->permissions = new stdClass();
        $module->permissions->push = true;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($repository)
            )
            ->willReturn($module)
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByUrl')
            ->with($this->equalTo($module->html_url))
            ->willReturn(false)
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
                'repo' => $repository,
                'owner' => $owner,
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
                $module->name
            ),
            $exception->getMessage()
        );
    }

    public function testViewActionSetsHttp404ResponseCodeIfModuleNotFound()
    {
        $vendor = 'foo';
        $module = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($module))
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                'zfmodule_mapper_module',
                $moduleMapper
            )
        ;

        $url = sprintf(
            '/%s/%s',
            $vendor,
            $module
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('not-found');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_404);
    }

    public function testViewActionSetsHttp404ResponseCodeIfRepositoryMetaDataNotFound()
    {
        $vendor = 'foo';
        $module = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($module))
            ->willReturn(new stdClass())
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
                $this->equalTo($module)
            )
            ->willReturn(null)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                'zfmodule_mapper_module',
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
            $module
        );

        $this->dispatch($url);

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('not-found');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_404);
    }

    public function testViewActionCanBeAccessed()
    {
        $vendor = 'foo';
        $module = 'bar';

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($module))
            ->willReturn(new stdClass())
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
                $this->equalTo($module)
            )
            ->willReturn(new stdClass())
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                'zfmodule_mapper_module',
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
            $module
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
}
