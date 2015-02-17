<?php

namespace ZfModuleTest\Service;

use EdpGithub\Api;
use EdpGithub\Client;
use EdpGithub\Http\Client as HttpClient;
use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\Http;
use ZfModule\Entity;
use ZfModule\Mapper;
use ZfModule\Service;
use ZfModuleTest\Mock;

class ModuleTest extends PHPUnit_Framework_TestCase
{
    public function testListAllModulesWithoutArgumentListsAllModulesFromDatabase()
    {
        $module = $this->getMockBuilder(Entity\Module::class)->getMock();

        $modules = [
            $module,
        ];

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo(null),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn($modules)
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame($modules, $service->allModules());
    }

    public function testListAllModulesWithArgumentListsModulesFromDatabaseLimited()
    {
        $limit = 9000;

        $module = $this->getMockBuilder(Entity\Module::class)->getMock();

        $modules = [
            $module,
        ];

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo($limit),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn($modules)
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame($modules, $service->allModules($limit));
    }

    public function testListUserModulesListsCurrentUsersModulesFromApiFoundInDatabase()
    {
        $repository = $this->repository();

        $module = $this->getMockBuilder(Entity\Module::class)->getMock();

        $modules = [
            $module,
        ];

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($repository->name))
            ->willReturn($module)
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->equalTo([
                'type' => 'all',
                'per_page' => 100,
            ]))
            ->willReturn(new Mock\Collection\RepositoryCollection([$repository]))
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $githubClient
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->willReturn($currentUserService)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame($modules, $service->currentUserModules());
    }

    public function testListUserModulesDoesNotLookupModulesFromApiWhereUserHasNoPushPrivilege()
    {
        $repository = $this->repository();

        $repository->permissions->push = false;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->never())
            ->method('findByName')
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->equalTo([
                'type' => 'all',
                'per_page' => 100,
            ]))
            ->willReturn(new Mock\Collection\RepositoryCollection([$repository]))
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $githubClient
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->willReturn($currentUserService)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame([], $service->currentUserModules());
    }

    public function testListUserModulesDoesNotLookupModulesFromApiThatAreForks()
    {
        $repository = $this->repository();

        $repository->fork = true;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->never())
            ->method('findByName')
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->equalTo([
                'type' => 'all',
                'per_page' => 100,
            ]))
            ->willReturn(new Mock\Collection\RepositoryCollection([$repository]))
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $githubClient
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->willReturn($currentUserService)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame([], $service->currentUserModules());
    }

    public function testListUserModulesDoesNotListModulesFromApiNotFoundInDatabase()
    {
        $repository = $this->repository();

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($repository->name))
            ->willReturn(false)
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->equalTo([
                'type' => 'all',
                'per_page' => 100,
            ]))
            ->willReturn(new Mock\Collection\RepositoryCollection([$repository]))
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $githubClient
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->willReturn($currentUserService)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame([], $service->currentUserModules());
    }

    public function testIsModuleQueriesGitHubApi()
    {
        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repository = $this->repository();

        $path = sprintf(
            'search/code?q=repo:%s/%s filename:Module.php "class Module"',
            $repository->owner->login,
            $repository->name
        );

        $response = $this->getMockBuilder(Http\Response::class)->getMock();

        $httpClient = $this->getMockBuilder(HttpClient::class)->getMock();

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with($this->equalTo($path))
            ->willReturn($response);

        $githubClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $githubClient
            ->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($httpClient)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $service->isModule($repository);
    }

    /**
     * @dataProvider providerIsModuleReturnsTrueIfResultCountIsGreaterThanZero
     *
     * @param bool $isModule
     * @param array $data
     */
    public function testIsModuleReturnValueDependsOnTotalCountInResponseBody($isModule, $data)
    {
        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repository = $this->repository();

        $response = $this->getMockBuilder(Http\Response::class)->getMock();

        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($data))
        ;

        $httpClient = $this->getMockBuilder(HttpClient::class)->getMock();

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $githubClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $githubClient
            ->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($httpClient)
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame($isModule, $service->isModule($repository));
    }

    /**
     * @return array
     */
    public function providerIsModuleReturnsTrueIfResultCountIsGreaterThanZero()
    {
        return [
            [
                false,
                [],
            ],
            [
                false,
                [
                    'total_count' => 0,
                ],
            ],
            [
                true,
                [
                    'total_count' => 1,
                ],
            ],
        ];
    }

    public function testRegisterInsertsModule()
    {
        $repository = $this->repository();

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByUrl')
            ->with($this->equalTo($repository->html_url))
            ->willReturn(null)
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('insert')
            ->with($this->isInstanceOf(Entity\Module::class))
        ;

        $githubClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $module = $service->register($repository);

        $this->assertSame($repository->name, $module->getName());
        $this->assertSame($repository->description, $module->getDescription());
        $this->assertSame($repository->html_url, $module->getUrl());
        $this->assertSame($repository->owner->login, $module->getOwner());
        $this->assertSame($repository->owner->avatar_url, $module->getPhotoUrl());
    }

    public function testRegisterUpdatesExistingModule()
    {
        $repository = $this->repository();

        $module = $this->getMockBuilder(Entity\Module::class)->getMock();

        $module
            ->expects($this->once())
            ->method('setName')
            ->with($this->equalTo($repository->name))
        ;

        $module
            ->expects($this->once())
            ->method('setDescription')
            ->with($this->equalTo($repository->description))
        ;

        $module
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->equalTo($repository->html_url))
        ;

        $module
            ->expects($this->once())
            ->method('setOwner')
            ->with($this->equalTo($repository->owner->login))
        ;
        $module
            ->expects($this->once())
            ->method('setPhotoUrl')
            ->with($this->equalTo($repository->owner->avatar_url))
        ;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByUrl')
            ->with($this->equalTo($repository->html_url))
            ->willReturn($module)
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('update')
            ->with($this->equalTo($module))
        ;

        $githubClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $service = new Service\Module(
            $moduleMapper,
            $githubClient
        );

        $this->assertSame($module, $service->register($repository));
    }

    /**
     * @return stdClass
     */
    private function repository()
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
}
