<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
use ApplicationTest\Integration\Util\Bootstrap;
use User\Mapper\User;
use Zend\Http;
use Zend\Paginator;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ZfModule\Mapper;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testIndexActionCanBeAccessed()
    {
        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('pagination')
            ->with(
                $this->equalTo(1),
                $this->equalTo(Controller\IndexController::MODULES_PER_PAGE),
                $this->equalTo(null),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn(new Paginator\Paginator(new Paginator\Adapter\Null()))
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('getTotal')
            ->willReturn(0)
        ;

        $moduleMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo(10),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn([])
        ;

        $userMapper = $this->getMockBuilder(User::class)->getMock();

        $userMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo(16),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn([])
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
            ->setService(
                'zfcuser_user_mapper',
                $userMapper
            )
        ;

        $this->dispatch('/');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }

    public function testFeedActionCanBeAccessed()
    {
        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('pagination')
            ->with(
                $this->equalTo(1),
                $this->equalTo(Controller\IndexController::MODULES_PER_PAGE),
                $this->equalTo(null),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn([])
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $this->dispatch('/feed');

        $this->assertControllerName(Controller\IndexController::class);
        $this->assertActionName('feed');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
