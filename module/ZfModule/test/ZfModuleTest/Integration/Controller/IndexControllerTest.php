<?php

namespace ZfModuleTest\Integration\Controller;

use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use stdClass;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ZfModule\Controller;
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
        $this->dispatch('/module');

        $this->assertControllerName('ZfModule\Controller\Index');
        $this->assertActionName('index');
    }

    public function testOrganizationActionCanBeAccessed()
    {
        $owner = 'foo';

        $url = sprintf(
            '/module/list/%s',
            $owner
        );

        $this->dispatch($url);

        $this->assertControllerName('ZfModule\Controller\Index');
        $this->assertActionName('organization');
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

        $repositoryRetriever = $this->getMockBuilder(Service\RepositoryRetriever::class)
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
                Service\RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $url = sprintf(
            '/%s/%s',
            $vendor,
            $module
        );

        $this->dispatch($url);

        $this->assertControllerName('ZfModule\Controller\Index');
        $this->assertActionName('view');
    }
}
