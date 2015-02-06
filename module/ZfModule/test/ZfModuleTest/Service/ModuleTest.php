<?php

namespace ZfModuleTest\Service;

use EdpGithub\Api;
use EdpGithub\Client;
use PHPUnit_Framework_TestCase;
use stdClass;
use ZfModule\Entity;
use ZfModule\Mapper;
use ZfModule\Service;

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

    public function testListUserModuleListsCurrentUsersModulesFromApiFoundInDatabase()
    {
        $name = 'foo';

        $repository = new stdClass();
        $repository->fork = false;
        $repository->permissions = new stdClass();
        $repository->permissions->push = true;
        $repository->name = $name;

        $module = $this->getMockBuilder(Entity\Module::class)->getMock();

        $modules = [
            $module,
        ];

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($name))
            ->willReturn($module)
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->logicalAnd(
                $this->arrayHasKey('type'),
                $this->arrayHasKey('per_page')
            ))
            ->willReturnCallback(function ($params) use ($repository) {
                if ('all' === $params['type'] && 100 === $params['per_page']) {
                    return [
                        $repository,
                    ];
                }

                return null;
            })
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
        $repository = new stdClass();
        $repository->fork = false;
        $repository->permissions = new stdClass();
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
            ->with($this->logicalAnd(
                $this->arrayHasKey('type'),
                $this->arrayHasKey('per_page')
            ))
            ->willReturnCallback(function ($params) use ($repository) {
                if ('all' === $params['type'] && 100 === $params['per_page']) {
                    return [
                        $repository,
                    ];
                }

                return null;
            })
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

    public function testListUsersModuleDoesNotLookupModulesFromApiThatAreForks()
    {
        $repository = new stdClass();
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
            ->with($this->logicalAnd(
                $this->arrayHasKey('type'),
                $this->arrayHasKey('per_page')
            ))
            ->willReturnCallback(function ($params) use ($repository) {
                if ('all' === $params['type'] && 100 === $params['per_page']) {
                    return [
                        $repository,
                    ];
                }

                return null;
            })
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
        $name = 'foo';

        $repository = new stdClass();
        $repository->fork = false;
        $repository->permissions = new stdClass();
        $repository->permissions->push = true;
        $repository->name = $name;

        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByName')
            ->with($this->equalTo($name))
            ->willReturn(false)
        ;

        $currentUserService = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserService
            ->expects($this->once())
            ->method('repos')
            ->with($this->logicalAnd(
                $this->arrayHasKey('type'),
                $this->arrayHasKey('per_page')
            ))
            ->willReturnCallback(function ($params) use ($repository) {
                if ('all' === $params['type'] && 100 === $params['per_page']) {
                    return [
                        $repository,
                    ];
                }

                return null;
            })
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
}
